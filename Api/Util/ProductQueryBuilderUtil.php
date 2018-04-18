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
use SqlQuery\SqlQuery;
use SqlQuery\SqlQueryInterface;


class ProductQueryBuilderUtil
{


    /**
     * This method returns the base query for retrieving some boxes.
     * Boxes potentially come in multiple flavors:
     *
     * - miniBox, each row containing:
     *      - product_id,
     *      - product_card_id,
     *      - reference,
     *      - label: the card label
     *      - product_slug,
     *      - product_card_slug,
     *      - image_id,
     *      - image_legend, the title attribute for the image
     *      - tax_ratio: number, 1 means no tax applied
     *      - original_price: the original price
     *      - price: the real price (original price with ek_product_variation applied to it)
     *      - base_price: the price, with taxes applied to it
     *      - sale_price: the base price, with discounts applied to it
     *      - discount_id: null means no discount applied
     *      - discount_label: null means no discount applied
     *      - discount_type: f|p|null
     *      - discount_value: number
     *      - codes: string containing codes. n means novelty
     *
     *
     * The returned query already stops after the joins part, in case you want to extend it...
     *
     *
     * @return SqlQueryInterface
     *
     */
    public static function getBaseQuery(array $userContext = null): SqlQueryInterface
    {

        if (null === $userContext) {
            $userContext = E::getUserContext();
        }
        $markers = [];

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
        self::applyContext($priceContext, $qPriceSubquery, $markers, 'price');
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
        self::applyContext($taxContext, $qTaxSubquery, $markers, 'tax');


        //--------------------------------------------
        // DISCOUNT CONTEXT
        //--------------------------------------------
//        $discountContext = E::getDiscountContext();
        $discountContext = [
            "datetime" => date('Y-m-d H:i:s'),
            "cond_user_group_id" => null,
            "cond_extra1" => null,
        ];

        $datetime = $discountContext['datetime'];
        unset($discountContext['datetime']);

        $qDiscountSubquery .= " 
        active = 1 
        and (cond_date_start is null or cond_date_start <= '$datetime')
        and (cond_date_end is null or cond_date_end >= '$datetime')
        and
        ";
        self::applyContext($discountContext, $qDiscountSubquery, $markers, 'discount');

        /**
         * As I said earlier, the limit of this system is if we have multiple discounts applied to a given product.
         * Here, we resolve such a case by ignoring all discounts but the one with the highest discount amount (basically
         * helping the customer here).
         */
        $qDiscountSubquery .= " order by d.value desc limit 0,1";

        $qDiscountSubqueryId = str_replace("WHAT", "id", $qDiscountSubquery);
        $qDiscountSubqueryLabel = str_replace("WHAT", "label", $qDiscountSubquery);
        $qDiscountSubqueryType = str_replace("WHAT", "type", $qDiscountSubquery);
        $qDiscountSubqueryValue = str_replace("WHAT", "value", $qDiscountSubquery);


        $field = "
p.id as product_id,
c.id as product_card_id,
p.reference,
if(
    '' != p.label,
    p.label,
    c.label
) as label,
p.slug as product_slug,
c.slug as product_card_slug,

coalesce (
    (select id from ek_product_card_image where product_card_id=c.id and product_id=p.id order by is_default desc limit 0,1),
    (select id from ek_product_card_image where product_card_id=c.id order by is_default desc limit 0,1)
)  as image_id,

coalesce (
    (select legend from ek_product_card_image where product_card_id=c.id and product_id=p.id order by is_default desc limit 0,1),
    (select legend from ek_product_card_image where product_card_id=c.id order by is_default desc limit 0,1)
)
as image_legend,

@taxRatio := coalesce (($qTaxSubquery), '1.00') as tax_ratio,




p.codes,
p._popularity as popularity,



($qDiscountSubqueryId) as discount_id,
($qDiscountSubqueryLabel) as discount_label,
@discountType := ($qDiscountSubqueryType) as discount_type,
@discountVal := ($qDiscountSubqueryValue) as discount_value,


p.price as original_price,
@price:= coalesce(
  ($qPriceSubquery),
  p.price
) as price, 
@basePrice := (ROUND(@price * @taxRatio, 2)) as base_price,
@salePrice := CAST(
    (
      case 
      when @discountType = 'f'
        then @basePrice - @discountVal
      when @discountType = 'p'
        then round(@basePrice - (@basePrice * @discountVal / 100), 2)
      when @discountType is null
        then @basePrice 
      end    
    ) as decimal(10,2)
 )
 as sale_price
        ";


        $sqlQuery = SqlQuery::create()
            ->addField($field)
            ->setTable("ek_product_card c")
            /**
             * Notice the difference between the commented line and the actual line:
             * with the commented line, the request only searches in products which are the default
             * of their cards, whereas with the uncommented line, we really search in all products;
             *
             * this is what we want: we for instance search all products having a discount of 20%,
             * and so the second line yields all those products (and can possibly display different
             * products of the same card next to each other), whereas the commented line would only
             * search in the default products.
             *
             *
             *
             */
//            ->addJoin("inner join ek_product p on p.id=c.product_id")
            ->addJoin("inner join ek_product p on p.product_card_id=c.id")
            ->addWhere(" and p.active=1 and c.active=1")
            /**
             * Note about group by,
             * it basically enabled to use aliases in the "having" clause (i.e. from my tests, having sale_price < 100
             * was only possible if the group by clause was present in the request).
             *
             *
             * See this topic: https://dba.stackexchange.com/questions/50391/why-does-mysql-allow-having-to-use-select-aliases
             *
             */
            ->addGroupBy('p.id')
            ->addMarkers($markers);

        return $sqlQuery;

    }


    public static function getMaxiQuery(array $userContext=null){
        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery($userContext);
        $sqlQuery->addField("
s.id as seller_id,
s.name as seller_name,
s.label as seller_label,
m.id as manufacturer_id,
m.name as manufacturer_name,
pct.name as product_card_type_name,
if(
    '' != p.description,
    p.description,
    c.description
) as description,
if(
    '' != p.meta_title,
    p.meta_title,
    c.meta_title
) as meta_title,
if(
    '' != p.meta_description,
    p.meta_description,
    c.meta_description
) as meta_description,
if(
    '' != p.meta_keywords,
    p.meta_keywords,
    c.meta_keywords
) as meta_keywords,
p.wholesale_price,
p.quantity,
p.weight,
p.out_of_stock_text,
p.active


        ");
        $sqlQuery->addJoin("
inner join ek_seller s on s.id=p.seller_id
inner join ek_product_card_type pct on pct.id=c.product_card_type_id
left join ek_manufacturer m on m.id=p.manufacturer_id
");
        return $sqlQuery;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private static function applyContext(array $context, string &$q, array &$markers, string $markerPrefix)
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
                $markerName = $markerPrefix . $c;
                $markers[$markerName] = $value;
                $q .= "$col = :$markerName";
            }
            $c++;
        }
    }
}