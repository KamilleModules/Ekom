<?php


namespace Module\Ekom\Api\Util;


use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Services\Exception\HooksException;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\CartModelEntity;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\Checkout\CheckoutUtil;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\DistanceEstimator\DistanceEstimatorInterface;
use Module\Ekom\Utils\E;
use Module\ThisApp\ThisAppConfig;


/**
 *
 *
 * miniBoxModel
 * --------------------
 *
 *
 * - id
 * - reference
 * - label
 * - slug (will be useful for generating link to the product page)
 * - card_slug
 * - discount_label:
 * - discount_type: null|p|f
 * - discount_value:
 * - sale_price: the computed sale price (the base_price with discount applied to it)
 * - codes: if contain letter n, means new...
 * - image: the medium size image uri
 *
 *
 * (used for internal computation, but left over for debug purposes)
 * - original_price: the original price
 * - price: the original price, with product_variation applied to it (if any)
 * - base_price: the price with tax applied to it
 * - tax_ratio: number|null if not applicable
 *
 */
class ProductQueryBuilderUtil
{


    public static function getBaseQuery(array &$markers = [], array $userContext = null): string
    {

        if (null === $userContext) {
            $userContext = E::getUserContext();
        }



        $qTaxSubquery = "select ratio from ek_tax_rule_condition where ";
        $qPriceSubquery = "select price from ek_product_variation where product_id=p.id and ";
        /**
         * Note: with the current architecture (meant to be fast, where price, discounts and taxes are part of the sql query so that
         * we can request on them), we can only have one discount per product (i.e. not multiple
         * discounts on one product).
         *
         * Think about it, (I believe) if you have potentially multiple discounts per product, then you cannot use
         * the discount price directly in your sql query (you need to resort to cache strategies),
         * and so you loose the ability to sort, order, filter by price/discount...
         *
         * So, that's the border line between a simple/fast ekom module and a complex/slow ekom module I guess...
         * Try to never cross that line and you'll be fine.
         *
         */
        $qDiscountSubquery = "
select 

label as discount_label, 
`type` as discount_type, 
`value` as discount_value
 
from ek_discount d
inner join ek_product_has_discount phd on phd.discount_id=d.id

where phd.product_id=p.id and

";
        $qDiscountSubquery = "
select 

WHAT
 
from ek_discount d
inner join ek_product_has_discount phd on phd.discount_id=d.id

where phd.product_id=p.id and

";


        //--------------------------------------------
        // PRICE CONTEXT
        //--------------------------------------------
//        $priceContext = E::getPriceContext();
        $priceContext = [
            /**
             * ThisApp will set this to either b2b or default (if the user is connected, or not connected)
             */
            "cond_identifier" => "b2b",
        ];
        self::applyContext($priceContext, $qPriceSubquery, $markers);
        $qPriceSubquery .= " limit 0,1";


        //--------------------------------------------
        // TAX CONTEXT
        //--------------------------------------------
//        $taxContext = E::getTaxContext();
        $taxContext = [
            "cond_user_group_id" => $userContext['user_group_id'],
            "cond_extra1" => null,
            "cond_extra2" => null,
            "cond_extra3" => null,
            "cond_extra4" => null,
        ];
        self::applyContext($taxContext, $qTaxSubquery, $markers);


        //--------------------------------------------
        // DISCOUNT CONTEXT
        //--------------------------------------------
//        $discountContext = E::getDiscountContext();
        $discountContext = [
            "datetime" => date('Y-m-d H:i:s'),
            "cond_user_group_id" => null,
            "cond_extra1" => "doo",
        ];

        $datetime = $discountContext['datetime'];
        unset($discountContext['datetime']);

        $qDiscountSubquery .= " 
        active = 1 
        and (cond_date_start is null or cond_date_start >= '$datetime')
        and (cond_date_end is null or cond_date_end <= '$datetime')
        and
        ";
        self::applyContext($discountContext, $qDiscountSubquery, $markers);

        $qDiscountSubqueryLabel = str_replace("WHAT", "label", $qDiscountSubquery);
        $qDiscountSubqueryType = str_replace("WHAT", "type", $qDiscountSubquery);
        $qDiscountSubqueryValue = str_replace("WHAT", "value", $qDiscountSubquery);


        $q = "
select 
p.id as product_id,
p.reference,
p.price as original_price,
@price:= coalesce(
  ($qPriceSubquery),
  p.price
) as price, 
p.slug,
c.id as card_id,
c.slug as card_slug,
c.label,
(select id from ek_product_card_image where product_card_id=c.id order by is_default desc limit 0,1) as image_id,
@taxRatio := ($qTaxSubquery) as tax_ratio,
($qDiscountSubqueryLabel) as discount_label,
@discountType := ($qDiscountSubqueryType) as discount_type,
@discountVal := ($qDiscountSubqueryValue) as discount_value,

@basePrice := (ROUND(@price * @taxRatio, 2)) as base_price,
(
  case 
  when @discountType = 'f'
    then @basePrice - @discountVal
  when @discountType = 'p'
    then round(@basePrice - (@basePrice * @discountVal / 100), 2)
  when @discountType is null
    then @basePrice 
  end    
) as sale_price






from ek_product_card c 
inner join ek_product p on p.id=c.product_id


        ";
        return $q;


    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private static function applyContext(array $context, string &$q, array &$markers)
    {
        // apply context, assuming the where is already written in the given query
        $c = 0;
        foreach ($context as $col => $value) {
            if (0 !== $c) {
                $q .= " and ";
            }
            if (null === $value) {
                $q .= "$col is null";
            } else {
                $markerName = "tax" . $c;
                $markers[$markerName] = $value;
                $q .= "$col = :$markerName";
            }
            $c++;
        }
    }
}