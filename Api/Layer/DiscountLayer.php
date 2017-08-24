<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class DiscountLayer
{


    /**
     * Prescription:
     * - do this once per day, at 04:00 am,
     * so that you start a day with fresh cached discount prices.
     *
     *
     * To give you an idea,
     * I executed this method on my local computer
     * with 1434 products, it took about 6 minute and 13 seconds to finish,
     * with tabatha cache off.
     *
     *
     */
    public function refreshDiscounts($shopId = null, array $slice = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $shopId = (int)$shopId;
        $pIds = QuickPdo::fetchAll("
select product_id from ek_shop_has_product
where shop_id=$shopId
", [], \PDO::FETCH_COLUMN);


        $start = 0;
        $end = count($pIds) - 1;
        if (null !== $slice) {
            list($start, $end) = $slice;
        }


        foreach ($pIds as $pId) {
            if ($pId >= $start && $pId <= $end) {
                a($pId);
                $this->refreshDiscountsByProductId($pId, $shopId);
            }
        }
    }


    public function refreshDiscountsByProductId($productId, $shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $productLayer = EkomApi::inst()->productLayer();
        $box = $productLayer->getProductBoxModelByProductId($productId, $shopId);

        $salePriceWithoutTax = $box['rawSalePrice'];
        $salePriceWithTax = $box['rawSalePriceWithTax'];

        return QuickPdo::update("ek_shop_has_product", [
            "_sale_price_without_tax" => $salePriceWithoutTax,
            "_sale_price_with_tax" => $salePriceWithTax,
        ], [
            ["shop_id", "=", $shopId],
            ["product_id", "=", $productId],
        ]);
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
        // todo: check hybrid system (in filesystem), only if use case appears
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
                $ret[$k] = $this->applyDiscountInfoToPrice($p, $discount);
            }
            return $ret;
        } else {
            return $this->applyDiscountInfoToPrice($price, $discount);
        }
    }


    /**
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
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

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


    private function applyDiscountInfoToPrice($price, $discountInfo, &$atLeastOneDiscountApplied = false)
    {
        $operand = $discountInfo['procedure_operand'];
        switch ($discountInfo['procedure_type']) {
            case 'amount':
                $price -= $operand;
                $atLeastOneDiscountApplied = true;
                break;
            case 'percent':
                $atLeastOneDiscountApplied = true;
                $price -= ($operand * $price) / 100;
                break;
            default:
                XLog::error("[Ekom module] - DiscountLayer: unknown procedure type: " . $discountInfo['procedure_type']);
                break;
        }
        return $price;
    }

}