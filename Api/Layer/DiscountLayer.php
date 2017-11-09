<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\HashTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 *
 *
 * A discount item
 * ======================
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
 *
 */
class DiscountLayer
{



    public static function applyDiscountInfoToPrice($discountInfo, $price)
    {
        $operand = $discountInfo['operand'];
        switch ($discountInfo['type']) {
            case 'fixed':
            case 'amount':
                $price -= $operand;
                break;
            case 'percent':
                $price -= ($operand * $price) / 100;
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
        }, [
            'ek_category',
            'ek_category_has_product_card',
            'ek_shop_has_product',
            'ek_shop_has_product_lang',
            'ek_seller',
            'ek_product_type',
            'ek_product',
            'ek_product_lang',
            'ek_product_has_discount',
            'ek_product_card_has_discount',
            'ek_shop_has_product_card',
        ]);
    }


    public function getProductsInfoHavingDiscount($shopId = null, $langId = null)
    {

        $shopId = E::getLangId($shopId);
        $langId = E::getLangId($langId);


        return A::cache()->get("Ekom.DiscountLayer.getProductsInfoHavingDiscount.$shopId.$langId", function () use ($shopId, $langId) {


            /**
             * Discounts are dispatched in products, cards and categories.
             * We want to return some presentational info.
             */
            $getResults = function (array $options = [], $debug = false) use ($shopId, $langId) {

                $join = (array_key_exists('join', $options)) ? $options['join'] : '';
                $where = (array_key_exists('where', $options)) ? $options['where'] : '';

                $q = "
select
distinct 
p.id,
p.product_card_id, 
p.reference, 
COALESCE(NULLIF('', shpl.label), pl.label) as label,        
COALESCE(NULLIF('', shpl.description), pl.description) as label,        
shp.quantity,
shp.active,
shp._sale_price_without_tax,
shp._sale_price_with_tax,
shp._discount_badge,
s.name as seller,
pt.name as product_type


from ek_shop_has_product shp 
inner join ek_shop_has_product_lang shpl on shpl.shop_id=shp.shop_id and shpl.product_id=shp.product_id
inner join ek_seller s on s.id=shp.seller_id 
inner join ek_product_type pt on pt.id=shp.product_type_id
inner join ek_product p on p.id=shp.product_id
inner join ek_product_lang pl on pl.product_id=shp.product_id and pl.lang_id=shpl.lang_id
$join



where
shp.shop_id=$shopId 
and shpl.lang_id=$langId
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
inner join ek_shop_has_product_card shpc on shpc.shop_id=shpl.shop_id and shpc.product_card_id=p.product_card_id
",
                'where' => "
and pchd.active=1
and shpc.active=1
            ",
            ]);


            $catsContainingDiscounts = $this->getCategoriesInfoHavingDiscount($shopId, $langId);
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
inner join ek_shop_has_product_card shpc on shpc.shop_id=shpl.shop_id and shpc.product_card_id=p.product_card_id
",
                'where' => "
and p.product_card_id in ($sCardIds)
and shpc.active=1

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

        }, [
            'ek_shop_has_product',
            'ek_shop_has_product_lang',
            'ek_seller',
            'ek_product_type',
            'ek_product',
            'ek_product_lang',
            'ek_product_has_discount',
            'ek_product_card_has_discount',
            'ek_shop_has_product_card',
        ]);
    }

    public function getCategoriesInfoHavingDiscount($shopId = null, $langId = null)
    {

        $shopId = E::getLangId($shopId);
        $langId = E::getLangId($langId);

        return QuickPdo::fetchAll("
select   
c.id,      
c.name,      
cl.label,
cl.slug

from ek_category c 
inner join ek_category_lang cl on cl.category_id=c.id
inner join ek_category_has_discount chd on chd.category_id=c.id

where 

c.shop_id=$shopId 
and cl.lang_id=$langId 
and chd.active=1


        ");

    }


    public function getDiscountBadgesByCategoryId($categoryId, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $categoryId = (int)$categoryId;

        $catIds = EkomApi::inst()->categoryLayer()->getDescendantCategoryIdTree($categoryId);
        $sCatIds = implode(', ', $catIds);


        return QuickPdo::fetchAll("
select distinct

h._discount_badge

from ek_shop_has_product h 
inner join ek_shop_has_product_card hpc on hpc.shop_id=h.shop_id and hpc.product_id=h.product_id
inner join ek_category_has_product_card chpc on chpc.product_card_id=hpc.product_card_id


where h.shop_id=$shopId
and h.active=1 
and hpc.active=1
and chpc.category_id in ($sCatIds)
and h._discount_badge != ''

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
        EkomApi::inst()->initWebContext();
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
            EkomApi::inst()->initWebContext();
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
     * @return array|false, the applicable discount for a given product, or false the product has no
     * applicable discount bound to it.
     *
     * The returned discount if any looks like the discount item described at the top of this class.
     *
     */
    public function getApplicableDiscountByProductId($productId, $shopId = null, $langId = null)
    {


        $productId = (int)$productId;
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        return A::cache()->get("Module.Ekom.Api.Layer.DiscountLayer.getDiscountByProductId.$shopId.$langId.$productId", function () use ($shopId, $langId, $productId) {


            /**
             * Discount applying at the product level is the most specific.
             */
            $discount = QuickPdo::fetch("
select 
d.id as discount_id,  
d.type,        
d.operand,        
d.target,
l.label,
h.conditions
        
from         
ek_discount d 
inner join ek_product_has_discount h on h.discount_id=d.id
inner join ek_discount_lang l on l.discount_id=d.id 

where h.product_id=$productId
and h.active=1
and d.shop_id=$shopId        
and l.lang_id=$langId        

order by d.id desc 
        
        
        ");


            $level = null;
            if (false === $discount) {

                /**
                 * No discount set at the product level?
                 * Maybe there is one at the card level?
                 */
                $discount = QuickPdo::fetch("
select 
d.id as discount_id,
d.type,        
d.operand,        
d.target,
l.label,
h.conditions
        
from         
ek_discount d 
inner join ek_product_card_has_discount h on h.discount_id=d.id
inner join ek_discount_lang l on l.discount_id=d.id 
inner join ek_product p on p.product_card_id=h.product_card_id

where p.id=$productId
and h.active=1
and d.shop_id=$shopId        
and l.lang_id=$langId
        
order by d.id desc         
        
        ");

                if (false === $discount) {

                    /**
                     * No discount set at the card level?
                     * Maybe there is one at the category level?
                     *
                     */
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
                        $discount = QuickPdo::fetch("
select 
d.id as discount_id,
d.type,        
d.operand,        
d.target,
l.label,
h.conditions
        
from         
ek_discount d 
inner join ek_category_has_discount h on h.discount_id=d.id
inner join ek_discount_lang l on l.discount_id=d.id 

where h.category_id in ($sCats) 
and h.active=1
and d.shop_id=$shopId        
and l.lang_id=$langId        
        
order by d.id desc        
        
        ");
                        if (false !== $discount) {
                            $level = "category";
                        }

                    }

                } else {
                    $level = "card";
                }

            } else {
                $level = 'product';
            }


            if (false !== $discount) {
                $discount['level'] = $level;
            }
            return $discount;

        }, [
            "ek_product",
            'ek_product_card',
            'ek_category',
            'ek_discount',
            'ek_discount_lang',
            'ek_product_has_discount',
            'ek_product_card_has_discount',
            'ek_category_has_discount',
        ]);
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
        EkomApi::inst()->initWebContext();

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


}