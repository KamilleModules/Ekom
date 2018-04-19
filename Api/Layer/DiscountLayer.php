<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\HashTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Helper\DateSegmentHelper;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 *
 *
 * discountItem
 * ======================
 * @see EkomModels::discountItem()
 *
 * Badge syntax
 * ==================
 *
 * - badgeSyntax: <typeLetter> <levelLetter> <operand>
 * - typeLetter: one letter representing the procedure type applied:
 *                  - p: percent
 *                  - f: fixed
 *                  - u: unknown
 *
 * - levelLetter: one letter representing the level on which the discount was applied:
 *                  - p: product
 *                  - c: card
 *                  - t: category
 *                  - u: unknown
 * - operand: the procedure operand (see database.md for more info)
 *
 *
 */
class DiscountLayer
{



    public static function getDiscountTypes()
    {
        return [
            "f" => "Réduction fixe",
            "p" => "Réduction proportionnelle",
        ];
    }

    public static function getItemsByShopId($shopId, $langId)
    {
        $shopId = (int)$shopId;
        $langId = (int)$langId;
        return QuickPdo::fetchAll("
select d.id, l.label 
from ek_discount d 
inner join ek_discount_lang l on l.discount_id=d.id
where d.shop_id=$shopId
and l.lang_id=$langId
        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function countDiscountByShopId($shopId)
    {
        $shopId = (int)$shopId;
        return QuickPdo::fetch("
select count(*) as count 
from ek_discount 
where shop_id=$shopId        
        ", [], \PDO::FETCH_COLUMN);
    }


    public static function getHumanIdentifier($discountId)
    {
        $discountId = (int)$discountId;
        $s = "";
        if (false !== ($row = QuickPdo::fetch("select * from ek_discount where id=$discountId"))) {
            $type = $row['type'];
            switch ($type) {
                case "percent":
                    $s = $row['operand'] . "% (" . $row['target'] . ")";
                    break;
                case "amount":
                    // we want symbolic representation here
                    $s = E::price($row['operand'], false) . " (" . $row['target'] . ")";
                    break;
                default:
                    throw new \Exception("Unknown discount type: $type");
                    break;
            }
        }
        return $s;
    }

    public static function getBadge(array $discount)
    {
        $symbol = "u"; // unknown
        if ('percent' === $discount['type']) {
            $symbol = "p";
        } elseif ('fixed' === $discount['type']) {
            $symbol = "f";
        }
        $level = "u";
        switch ($discount['level']) {
            case "product":
                $level = "p";
                break;
            case "card":
                $level = "c";
                break;
            case "category":
                $level = "t";
                break;
        }


        return $symbol . $level . $discount['operand'];
    }

    public static function applyDiscountInfoToPrice($discountInfo, $price)
    {
        $operand = $discountInfo['operand'];
        switch ($discountInfo['type']) {
            case 'fixed':
            case 'amount':
                $price -= $operand;
                if ($price < 0) {
                    $price = 0;
                }
                break;
            case 'percent':
                $price -= ($operand * $price) / 100;
                if ($price < 0) {
                    $price = 0;
                }
                $price = E::trimPrice($price);
                break;
            default:
                XLog::error("[Ekom module] - DiscountLayer: unknown procedure type: " . $discountInfo['type']);
                break;
        }
        return E::trimPrice($price);
    }


    /**
     * @param $discount (discount item as described at the top of this document)
     * @param $price
     * @return array
     * - type: the procedure type
     * - discountPrice: number
     * - savingPercent: number
     * - savingFixed: number
     */
    public static function applyDiscount($discount, $price)
    {
        $discountPrice = self::applyDiscountInfoToPrice($discount, $price);
        $diff = E::trimPrice($price - $discountPrice);
        if (0.0 !== (float)$price) {
            $diffPercent = $diff / $price * 100;
        } else {
            $diffPercent = 0;
        }
        $savingPercent = E::trimPercent($diffPercent);
        $savingFixed = $diff;

        return [
            'type' => $discount['type'],
            'discountPrice' => $discountPrice,
            'savingPercent' => $savingPercent,
            'savingFixed' => $savingFixed,
        ];
    }

    /**
     * Return the list of badges filtered according to the given options.
     *
     * @param array $options (all options are optional)
     *              - shop_id: restrict the search to the given shop id
     *              - lang_id: use the given lang_id
     *              - categoryName: restrict the search to the given category name
     *              - procedureType: percent|amount, restrict the search to the given procedure type
     *
     *
     * @return array, the list of badges
     */
    public function getDiscountBadges(array $options = [])
    {
        $options = array_merge([
            'shop_id' => null,
            'lang_id' => null,
            'categoryId' => null,
            'categoryName' => null,
            'procedureType' => null,
        ], $options);

        $hash = HashTool::getHashByArray($options);

        return A::cache()->get("Ekom.DiscountLayer.getDiscountBadges.$hash", function () use ($options) {

            $shopId = E::getShopId($options['shop_id']);
            $langId = E::getLangId($options['lang_id']);

            $categoryId = $options['categoryId'];
            $category = $options['categoryName'];
            $procedureType = $options['procedureType'];


            $allowedProductCardIds = null;
            if (null !== $category) {
                $allowedProductCardIds = [];
                EkomApi::inst()->categoryLayer()->collectProductCardIdsDescendantsByCategoryName($allowedProductCardIds, $category, $shopId);
            } elseif (null !== $categoryId) {
                $allowedProductCardIds = [];
                EkomApi::inst()->categoryLayer()->collectProductCardIdsDescendantsByCategoryIds($allowedProductCardIds, [$categoryId]);
            }


            $pInfos = $this->getProductsInfoHavingDiscount($shopId, $langId);
            $allBadges = [];
            foreach ($pInfos as $info) {
                $badge = $info['_discount_badge'];


                // filtering by cats
                if (null !== $allowedProductCardIds && false === in_array($info['product_card_id'], $allowedProductCardIds)) {
                    continue;
                }

                // filtering by procedure type
                if (null !== $procedureType) {
                    $firstLetter = substr($badge, 0, 1);
                    if (
                        ('percent' === $procedureType && 'p' !== $firstLetter) ||
                        ('amount' === $procedureType && 'f' !== $firstLetter)
                    ) {
                        continue;
                    }
                }

                if ('' !== $badge) {
                    $badges = explode(',', $badge);
                    $allBadges = array_merge($allBadges, $badges);
                }
            }
            $allBadges = array_unique($allBadges);
            sort($allBadges);
            return $allBadges;
        });
    }


    public function getProductsInfoHavingDiscount()
    {
        return A::cache()->get("Ekom.DiscountLayer.getProductsInfoHavingDiscount", function () {


            /**
             * Discounts are dispatched in products, cards and categories.
             * We want to return some presentational info.
             */
            $getResults = function (array $options = [], $debug = false) {

                $join = (array_key_exists('join', $options)) ? $options['join'] : '';
                $where = (array_key_exists('where', $options)) ? $options['where'] : '';

                $q = "
select
distinct 
p.id,
p.product_card_id, 
p.reference, 
p.label,        
p.description,        
p.quantity,
p.active,
p._sale_price_without_tax,
p._sale_price_with_tax,
p._discount_badge,
s.name as seller,
pt.name as product_type


from ek_product p
inner join ek_seller s on s.id=shp.seller_id 
inner join ek_product_type pt on pt.id=shp.product_type_id
$join



where 1
$where

        
        ";

                if (true === $debug) {
                    a($q);
                }
                return QuickPdo::fetchAll($q);

            };


            $productRows = $getResults([
                'join' => "inner join ek_product_has_discount phd on phd.product_id=p.id",
                'where' => "and phd.active=1",
            ]);


            $cardRows = $getResults([
                'join' => "            
inner join ek_product_card_has_discount pchd on pchd.product_card_id=p.product_card_id
inner join ek_product_card c on c.id=p.product_card_id
",
                'where' => "
and pchd.active=1
and c.active=1
            ",
            ]);


            $catsContainingDiscounts = $this->getCategoriesInfoHavingDiscount();
            $catIds = [];
            $allSubCatIds = [];
            foreach ($catsContainingDiscounts as $info) {
                $catIds[] = $info['id'];
                EkomApi::inst()->categoryLayer()->doCollectDescendants($info['id'], $allSubCatIds);
            }
            $allCardIds = [];
            EkomApi::inst()->categoryLayer()->collectProductCardIdsDescendantsByCategoryIds($allCardIds, $catIds);
            sort($allCardIds);

            $sCardIds = implode(', ', $allCardIds);
            $catRows = $getResults([
                'join' => "                      
inner join ek_product_card c on c.id=p.product_card_id
",
                'where' => "
and p.product_card_id in ($sCardIds)
and c.active=1

            ",
            ]);


            $all = array_merge($catRows, $productRows, $cardRows);
            $in = [];
            $all = array_filter($all, function ($v) use (&$in) {
                if (in_array($v['id'], $in)) {
                    return false;
                } else {
                    $in[] = $v['id'];
                    return true;
                }
            });
            return $all;

        });
    }

    public function getCategoriesInfoHavingDiscount()
    {

        return QuickPdo::fetchAll("
select   
c.id,      
c.name,      
c.label,
c.slug

from ek_category c
inner join ek_category_has_discount chd on chd.category_id=c.id

where chd.active=1


        ");

    }


    public function getDiscountBadgesByCategoryId($categoryId)
    {
        $categoryId = (int)$categoryId;

        $catIds = EkomApi::inst()->categoryLayer()->getDescendantCategoryIdTree($categoryId);
        $sCatIds = implode(', ', $catIds);


        return QuickPdo::fetchAll("
select distinct

p._discount_badge

from ek_product p
inner join ek_product_card c on c.product_id=p.product_id
inner join ek_category_has_product_card chpc on chpc.product_card_id=c.id


where p.active=1 
and c.active=1
and chpc.category_id in ($sCatIds)
and p._discount_badge != ''

        ", [], \PDO::FETCH_COLUMN);


    }

    /**
     * Prescription:
     * - do this once per day, at 04:00 am,
     * so that you start a day with fresh cached discount prices.
     *
     *
     *
     * To give you an idea,
     * I executed this method on my local computer
     * with 1434 products, it took about 6 minute and 13 seconds to finish,
     * with tabatha cache off.
     *
     *
     */
    public function refreshDiscounts($shopId = null, array $slice = null, array $pIds = null)
    {

        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $shopId = (int)$shopId;


        if (null === $pIds) {

            $pIds = QuickPdo::fetchAll("
select product_id from ek_shop_has_product
where shop_id=$shopId
", [], \PDO::FETCH_COLUMN);
        }


        $start = 0;
        $end = count($pIds) - 1;
        if (null !== $slice) {
            list($start, $end) = $slice;
        }

        foreach ($pIds as $pId) {
            if (null !== $slice) {
                if ($pId < $start && $pId > $end) {
                    continue;
                }
            }
            $this->refreshDiscountsByProductId($pId, $shopId);
        }
    }


    public function refreshDiscountsByProductId($productId, $shopId = null)
    {

        $shopId = E::getShopId($shopId);
        $productLayer = EkomApi::inst()->productLayer();
        $box = $productLayer->getProductBoxModelByProductId($productId, $shopId);
        if (false === array_key_exists('errorCode', $box)) {


            if (true === $box['discountHasDiscount']) {

                $discount = $box['discount'];
                /**
                 * - discount_id: int
                 * - label: string
                 * - type: the procedure type (percentage|fixed)
                 * - operand: numeric, the procedure operand
                 * - target: string representing the target to which the procedure should be applied
                 * - level: string, the level at which the discount was applied, can be one of:
                 *          - product
                 *          - card
                 *          - category
                 *
                 * - conditions: string, text of conditions deciding whether or not the discount applies to the product
                 *
                 *
                 */


                $badgeDetails = [$discount];
                $badges = [];
                foreach ($badgeDetails as $info) {
                    $prefix = '';
                    switch ($info['type']) {
                        case "fixed":
                            $prefix = 'f';
                            break;
                        default:
                            $prefix = 'p';
                            break;
                    }

                    $compo2 = '';
                    switch ($info['level']) {
                        case "product":
                            $compo2 = 'p';
                            break;
                        case "card":
                            $compo2 = 'c';
                            break;
                        case "category":
                            $compo2 = 't';
                            break;
                        default:
                            break;
                    }


                    $badges[] = $prefix . $compo2 . $info['value'];
                }

                if ($badges) {
                    $discountBadge = implode(',', $badges);
                    return QuickPdo::update("ek_shop_has_product", [
                        /**
                         * Note: this is just the applicable discount, not the actual discount applied
                         * to the product.
                         *
                         */
                        "_discount_badge" => $discountBadge,
                    ], [
                        ["shop_id", "=", $shopId],
                        ["product_id", "=", $productId],
                    ]);
                }
            }
        }
        return false;
    }


    public function applyDiscountsByProductId($productId, $priceWithoutTax, $priceWithTax, array &$badges = [], $shopId = null, $langId = null)
    {
        $layerDiscount = EkomApi::inst()->discountLayer();
        $discounts = $layerDiscount->getDiscountsByProductId($productId, $shopId, $langId);
        /**
         * note that in this algorithm, the discount for the withTax price
         * is applied on the WithTax price directly (see my note on algorithms in
         * class-modules/Ekom/doc/my/thoughts/things-i-discovered-with-prices.md)
         */
        $salePrices = $layerDiscount->applyDiscountsToPrice($discounts, [
            E::trimPrice($priceWithoutTax),
            E::trimPrice($priceWithTax),
        ], $badges);
        return $salePrices;
    }


    /**
     * If badges is fed, this means that the price has been modified,
     * if still empty after the call to this function,
     * this means the price has not been modified.
     */
    public function applyDiscountsToPrice(array $discounts, $price, array &$badges = [])
    {
        $layerDiscount = EkomApi::inst()->discountLayer();
        $atLeastOneDiscountApplied = false;
        foreach ($discounts as $d) {
            $t = false;
            $operand = $d['procedure_operand'];
            $target = $d['target']; // implicit/ignored for now with ekom order model4

            $price = $layerDiscount->applyDiscountToPrice($d, $price, $t);
            if (false !== $t) {
                $badges[] = [
                    "type" => $d['procedure_type'],
                    "value" => $operand,
                    "label" => $d['label'],
                    "level" => $d['level'],
                ];
                $atLeastOneDiscountApplied = true;
            }
        }
        return $price;
    }


    /**
     * Apply discount to price and return the discounted price.
     *
     * Reminder, this method should be called dynamically and should not be cached (unless you know
     * exactly what you are doing: we have user connexion state, dates, maybe other things...)
     *
     *
     * @param array $discount
     * @param $price , float or array of float
     * @param bool $atLeastOneDiscountApplied
     * @return float|array of floats
     *              The price, or an array of prices (if the $price argument was passed
     *              as an array)
     */
    public function applyDiscountToPrice(array $discount, $price, &$atLeastOneDiscountApplied = false)
    {
        // @todo-ling: check hybrid system (in filesystem), only if use case appears
        //--------------------------------------------
        // ELIMINATE BY FILTERS
        //--------------------------------------------
        // user group
        if (null !== $discount['user_group_id']) {
            if (true === SessionUser::isConnected()) {
                $userId = SessionUser::getValue('id');
                $userGroupIds = EkomApi::inst()->userLayer()->getUserGroupIds($userId);

                if (false === in_array($discount['user_group_id'], $userGroupIds)) {
                    return false;
                }
            } else {
                return false;
            }
        }


        // currency
        if (null !== $discount['currency_id']) {

            $currencyId = ApplicationRegistry::get("ekom.currency_id");
            if ((string)$currencyId !== (string)$discount['currency_id']) {
                return false;
            }
        }

        // date
        if (null !== $discount['date_start'] || null !== $discount['date_end']) {
            $dateStart = $discount['date_start'];
            $dateEnd = $discount['date_end'];
            $curDate = date("Y-m-d H:i:s");
            if (null !== $dateStart && null !== $dateEnd) {
                if ($curDate < $dateStart || $curDate > $dateEnd) {
                    return false;
                }
            } else if (null !== $dateStart) {
                if ($curDate < $dateStart) {
                    return false;
                }
            } else {
                if ($curDate > $dateEnd) {
                    return false;
                }
            }
        }


        //--------------------------------------------
        // APPLY PROCEDURE ON TARGET
        //--------------------------------------------
        if (is_array($price)) {
            $ret = [];
            foreach ($price as $k => $p) {
                $ret[$k] = self::applyDiscountInfoToPrice($p, $discount, $atLeastOneDiscountApplied);
            }
            return $ret;
        } else {
            return self::applyDiscountInfoToPrice($price, $discount, $atLeastOneDiscountApplied);
        }
    }


    /**
     * @return array|false, discountItem, the applicable discount for a given product, or false the product has no
     * applicable discount bound to it.
     *
     * @see EkomModels::discountItem()
     *
     */
    public function getApplicableDiscountByProductId(int $productId, array $discountContext)
    {

        $hash = HashTool::getHashByArray($discountContext);

        return A::cache()->get("Module.Ekom.Api.Layer.DiscountLayer.getDiscountByProductId.$productId.$hash", function () use ($productId, $discountContext) {


            /**
             * Discount applying at the product level is the most specific.
             */

            $q = "
select 
d.id,  
d.label,        
d.code,        
d.type,        
d.value
        
from         
ek_discount d 
inner join ek_product_has_discount h on h.discount_id=d.id 

where h.product_id=$productId
and d.active=1
    
        
        
        ";

            $markers = [];
            self::decorateQueryWithDiscountConditionsByDiscountContext($q, $markers, $discountContext);

            /**
             * As for now in ekom, we a given product can only be applied ONE discount at the time (i.e. discounts are not cumulable yet).
             * Note: this might change in the future as the need for cumulable discounts appear...
             */
            $q .= "
order by d.priority desc 
limit 1
";

            return QuickPdo::fetch($q, $markers);
        });
    }


    /**
     * @deprecated, but keep it here just in case ekom switches again to a system that allows multiple discounts
     * per product.
     *
     *
     * Return the discounts to potentially (if the condition matches) apply to a given product, ordered by ascending order_phase.
     *
     * Note that there can only be one discount per order_phase (by design).
     * See latest $date-database.md document for more info.
     *
     *
     * Each entry contains the following structure (see latest $date-database.md for more information):
     *
     * - discount_id: id of the discount
     * - user_group_id: null
     * - currency_id:  null
     * - date_start:  null
     * - date_end:  null
     * - procedure_type:  percent|amount|...
     * - procedure_operand:
     * - target: priceWithoutTax|...
     * - label:
     * - order_phase:
     * - level: product|card|category
     *
     */
    public function getDiscountsByProductId($productId, $shopId = null, $langId = null)
    {


        // get the discounts that apply to the product,
        // then get the discounts that apply to the product card,
        // then get the discounts that apply to the category

        /**
         * We need to get them as 3 separate layers, because we then want to combine them
         * in a certain way.
         */

        $productId = (int)$productId;
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        return A::cache()->get("Module.Ekom.Api.Layer.DiscountLayer.getDiscountsByProductId.$shopId.$langId.$productId", function () use ($shopId, $langId, $productId) {


            $cardId = EkomApi::inst()->product()->readColumn("product_card_id", [
                ["id", "=", $productId],
            ]);


            // get the discounts that apply to the product,
            $discountsProduct = QuickPdo::fetchAll("
select 
d.id as discount_id,
d.user_group_id,        
d.currency_id,        
d.date_start,        
d.date_end,        
d.procedure_type,        
d.procedure_operand,        
d.target,
l.label,
h.order_phase
        
from         
ek_discount d 
inner join ek_product_has_discount h on h.discount_id=d.id
inner join ek_discount_lang l on l.discount_id=d.id 

where h.product_id=$productId
and h.active=1
and d.shop_id=$shopId        
and l.lang_id=$langId        
        
        
        ");


            // get the discounts that apply to the card,
            $discountsCard = QuickPdo::fetchAll("
select 
d.id as discount_id,
d.user_group_id,        
d.currency_id,        
d.date_start,        
d.date_end,        
d.procedure_type,        
d.procedure_operand,        
d.target,
l.label,
h.order_phase
        
from         
ek_discount d 
inner join ek_product_card_has_discount h on h.discount_id=d.id
inner join ek_discount_lang l on l.discount_id=d.id 

where h.product_card_id=$cardId
and h.active=1
and d.shop_id=$shopId        
and l.lang_id=$langId        
        
        
        ");


            /**
             * Get the discounts applying to categories
             */
            $discountsCategory = [];
            /**
             * Cats is the category of the product, or a category above (parent)
             */
            $cats = EkomApi::inst()->categoryLayer()->getCategoryIdTreeByProductId($productId);
            if (0 === count($cats)) {
                XLog::error("[Ekom module] - DiscountLayer.getDiscountsByProductId: no categories found for product $productId");
            }

            if ($cats) {

                $sCats = implode(', ', $cats);
                // get the discounts that apply to the product,
                $discountsCategory = QuickPdo::fetchAll("
select 
d.id as discount_id,
d.user_group_id,        
d.currency_id,        
d.date_start,        
d.date_end,        
d.procedure_type,        
d.procedure_operand,        
d.target,
l.label,
h.order_phase
        
from         
ek_discount d 
inner join ek_category_has_discount h on h.discount_id=d.id
inner join ek_discount_lang l on l.discount_id=d.id 

where h.category_id in ($sCats) 
and h.active=1
and d.shop_id=$shopId        
and l.lang_id=$langId        
        
        
        ");

            }

//            a([
//                'product' => $discountsProduct,
//                'card' => $discountsCard,
//                'category' => $discountsCategory,
//            ]);

            $ret = [];
            $this->mergeIfNotExist($ret, $discountsProduct, 'product');
            $this->mergeIfNotExist($ret, $discountsCard, 'card');
            $this->mergeIfNotExist($ret, $discountsCategory, 'category');
            ksort($ret);

            return $ret;

        }, [
            "ek_product.delete.$productId",
            'ek_discount',
            'ek_discount_lang',
            'ek_product_has_discount',
            'ek_product_card_has_discount',
            'ek_category_has_discount',
            // very little change that ek_category.update should trigger this (the only case is if you change the id of the category, which admittedly almost never happens in prod day to day life)
            // actually, that's a pattern
            'ek_category.delete',
            'ek_product_card.delete',
        ]);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function mergeIfNotExist(array &$ret, array $discounts, $level)
    {
        foreach ($discounts as $discount) {
            if (!array_key_exists($discount['order_phase'], $ret)) {
                $discount['level'] = $level;
                $ret[$discount['order_phase']] = $discount;
            }
        }
    }


    private static function decorateQueryWithDiscountConditionsByDiscountContext(&$q, array &$markers, array $discountContext)
    {
        /**
         * Assuming the "where 1" is already set...
         */

        if (array_key_exists("date_segment", $discountContext)) {
            $datetime = DateSegmentHelper::resolveDateSegment($discountContext['date_segment']);
            $q .= " and (
(cond_date_start is null and cond_date_end is null) or        
(cond_date_start is null and cond_date_end is not null and '$datetime' <= cond_date_end ) or
(cond_date_start is not null and cond_date_end is null and '$datetime' >= cond_date_start ) or
(cond_date_start is not null and cond_date_end is not null and '$datetime' >= cond_date_start and '$datetime' <= cond_date_end) 
        )";
        }

        for ($i = 1; $i <= 1; $i++) {
            $name = "extra" . $i;
            if (array_key_exists($name, $discountContext)) {
                $marker = "discountmarker$i";
                $q .= " and cond_$name = :$marker";
                $markers[$marker] = $discountContext[$name];
            } else {
                $q .= "and cond_$name is null";
            }
        }
    }
}