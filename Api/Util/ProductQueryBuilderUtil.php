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
use Module\Ekom\Helper\ProductDetailsHelper;
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
     *      - product_reference_id
     *      - product_card_type_name,
     *      - product_card_type_label,
     *      - manufacturer_id,
     *      - provider_id,
     *      - seller_id,
     *      - seller_name,
     *      - seller_label,
     *      - reference,
     *      - _product_details, @see ProductDetailsHelper
     *      - quantity,
     *      - out_of_stock_text,
     *      - label: the card label,
     *      - description,
     *      - product_slug,
     *      - product_card_slug,
     *      - image_id,
     *      - image_legend, the title attribute for the image
     *      - tax_ratio: number, 1 means no tax applied
     *      - original_price: the original price
     *      - real_price: the real price (original price with ek_product_variation applied to it)
     *      - base_price: the price, with discounts applied to it
     *      - sale_price: the base price, with taxes applied to it
     *      - discount_id: null means no ekom discount applied.
     *                  If you want to know whether or not a discount was applied at all, use the
     *                  discount_value property (check against float 0.0).
     *
     *                  That's because data might be imported from another system (like prestashop for instance),
     *                  and depending on that system and the skill of the developer behind the import, you might loose
     *                  some data during the import, causing these inconsistencies.
     *
     *
     *      - discount_label: null means no ekom discount applied. Same notes as discount_id
     *      - discount_type: f|p|null
     *      - discount_value: number
     *      - codes: string containing codes. n means novelty
     *
     *      // and with options, you can add the following optional properties
     *      - tax_rule_condition_id: null|int
     *      - attr_string: string value labels separated with comma+space ", " as the separator
     *
     *
     *
     * The returned query already stops after the joins part, in case you want to extend it...
     *
     *
     * Important note:
     * ----------------
     * This query builder is mainly designed for the front.
     * However, the backoffice can also use it to display some list,
     * but beware that the user had a specific userContext which was shaping the created sqlQuery in the front,
     * but in the backoffice we don't have the userContext.
     * In this implementation, we don't even try to recreate it,
     * we basically say:
     *
     *      - whenever you use the QueryBuilder in the backoffice, you only get an approximate preview of what
     *          the user really saw (in terms of prices, taxes, discounts).
     *          But that's fine (at least for me for now).
     *
     *
     *
     *
     * @return SqlQueryInterface
     *
     */
    public static function getBaseQuery(array $options = []): SqlQueryInterface
    {
        $useTaxRuleConditionId = $options['useTaxRuleConditionId'] ?? false;
        $useAttributeString = $options['useAttributesString'] ?? true;


        $taxContext = E::getTaxContext();
        $priceContext = E::getPriceContext();
        $discountContext = E::getDiscountContext();


        $allSubQueriesInfo = [
            "markers" => [],
            "taxContext" => $taxContext, // read-only, ok?
            "priceContext" => $priceContext,
            "discountContext" => $discountContext,
        ];
        $useCustomTaxSubquery = false;
        Hooks::call("Ekom_ProductQueryBuilder_decorateSubqueriesInfo", $allSubQueriesInfo);

        $markers = $allSubQueriesInfo['markers'];

        if (array_key_exists('taxSubquery', $allSubQueriesInfo)) {
            $useCustomTaxSubquery = true;
            $qTaxSubquery = $allSubQueriesInfo['taxSubquery'];

        } else {
            $qTaxSubquery = "
select ratio 

from ek_tax_rule_condition rr
inner join ek_product_card cc on cc.tax_rule_id=rr.tax_rule_id
 
where cc.id=c.id and 
";
        }


        /**
         * if true, yields the tax_rule_condition_id field in the results
         */
        if (true === $useTaxRuleConditionId) {
            if (array_key_exists('taxCondSubquery', $allSubQueriesInfo)) {
                $qTaxCondSubquery = $allSubQueriesInfo['taxCondSubquery'];

            } else {
                $qTaxCondSubquery = "select id from ek_tax_rule_condition where ";
            }
        }

        $qPriceSubquery = "select price from ek_product_variation where product_reference_id=pr.id and ";
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
inner join ek_product_reference_has_discount phd on phd.discount_id=d.id

where phd.product_reference_id=pr.id and

";


        //--------------------------------------------
        // PRICE CONTEXT
        //--------------------------------------------

//        $priceContext = [
//            /**
//             * ThisApp will set this to either b2b or default (if the user is connected, or not connected)
//             */
//            "cond_identifier" => "b2b",
//        ];
        self::applyContext($priceContext, $qPriceSubquery, $markers, 'price');
        $qPriceSubquery .= " limit 0,1";


        //--------------------------------------------
        // TAX CONTEXT
        //--------------------------------------------

//        $taxContext ['cond_user_group_id'] = "4";
//        $taxContext = [
//            "cond_user_group_id" => $userContext['user_group_id'],
//            "cond_extra1" => null,
//            "cond_extra2" => null,
//            "cond_extra3" => null,
//            "cond_extra4" => null,
//        ];
        if (false === $useCustomTaxSubquery) {
            self::applyContext($taxContext, $qTaxSubquery, $markers, 'tax');
        }


        //--------------------------------------------
        // DISCOUNT CONTEXT
        //--------------------------------------------
//        $discountContext = [
//            "datetime" => date('Y-m-d H:i:s'),
//            "cond_user_group_id" => null,
//            "cond_extra1" => null,
//        ];

        $datetime = $discountContext['datetime'];
        unset($discountContext['datetime']);


        $qDiscountSubquery .= " 
        active = 1 
        and (cond_date_start is null or cond_date_start <= '$datetime')
        and (cond_date_end is null or cond_date_end >= '$datetime')
        ";


        /**
         * Note: here I'm setting the cond_user_group_id manually,
         * it allows me this:
         *      in the backoffice I set a discount with no values for the user group (null)
         *      to target ALL groups.
         *      If I let the default applyContext method handles the cond_user_group_id,
         *      the user group=null in the backoffice would not target ANY user group...
         */
        if ($discountContext['cond_user_group_id']) {
            $userGroupId = (int)$discountContext['cond_user_group_id'];
            $qDiscountSubquery .= "
        and (cond_user_group_id is null or cond_user_group_id=$userGroupId)
            ";
        }

        $qDiscountSubquery .= " and ";

        unset($discountContext['cond_user_group_id']);
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


        $optionalTaxCondSubquery = "";
        if (true === $useTaxRuleConditionId) {
            if (false === array_key_exists('taxCondSubquery', $allSubQueriesInfo)) {
                self::applyContext($taxContext, $qTaxCondSubquery, $markers, 'taxcond');
            }
            $optionalTaxCondSubquery = "($qTaxCondSubquery) as tax_rule_condition_id,";
        }


        $sAttributeString = "";
        if (true === $useAttributeString) {
            $sAttributeString .= "
coalesce(        
    ( 
      select 
        group_concat( distinct label separator ', ')
        from ek_product_attribute_value v 
        inner join ek_product_has_product_attribute h on h.product_attribute_value_id=v.id
        where h.product_id=p.id
            
    ),
    ''
) as attr_string,            
            ";
        }


        $field = "
p.id as product_id,
pr.id as product_reference_id,
c.id as product_card_id,
pct.name as product_card_type_name,
pct.label as product_card_type_label,
sel.id as seller_id,
sel.name as seller_name,
sel.label as seller_label,
p.manufacturer_id,
p.provider_id,
pr.reference,
pr._product_details,
$sAttributeString
pr.quantity,
p.out_of_stock_text,
if(
    '' != p.label,
    p.label,
    c.label
) as label,
if(
    '' != p.description,
    p.description,
    c.description
) as description,
p.slug as product_slug,
c.slug as product_card_slug,

coalesce (
    (select id from ek_product_card_image where product_card_id=c.id and is_default=1 order by position asc limit 0,1),
    (select id from ek_product_card_image where product_card_id=c.id order by position asc limit 0,1)
)  as image_id,

coalesce (
    (select legend from ek_product_card_image where product_card_id=c.id and is_default=1 order by position asc limit 0,1),
    (select legend from ek_product_card_image where product_card_id=c.id order by position asc limit 0,1)
)
as image_legend,

@taxRatio := coalesce (($qTaxSubquery), '1.00') as tax_ratio,
$optionalTaxCondSubquery



p.codes,
p._popularity as popularity,



($qDiscountSubqueryId) as discount_id,
($qDiscountSubqueryLabel) as discount_label,
@discountType := ($qDiscountSubqueryType) as discount_type,
@discountVal := ($qDiscountSubqueryValue) as discount_value,


pr.price as original_price,
@realPrice:= coalesce(
  ($qPriceSubquery),
  pr.price
) as real_price, 

@basePrice := CAST(
    (
      case 
      when @discountType = 'f'
        then @realPrice - @discountVal
      when @discountType = 'p'
        then round(@realPrice - (@realPrice * @discountVal / 100), 2)
      when @discountType is null
        then @realPrice 
      end    
    ) as decimal(10,2)
)
as base_price,
@salePrice := (ROUND(@basePrice * @taxRatio, 2)) as sale_price
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
            ->addJoin("
inner join ek_product p on p.product_card_id=c.id
inner join ek_product_reference pr on pr.product_id=p.id
inner join ek_product_card_type pct on pct.id=c.product_card_type_id
inner join ek_seller sel on sel.id=p.seller_id
            ")
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
            ->addGroupBy('pr.reference')
            ->addMarkers($markers);

        return $sqlQuery;

    }


    public static function getMaxiQuery()
    {
        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery();
        $sqlQuery->addField("
m.id as manufacturer_id,
m.name as manufacturer_name,
p.internal_reference,
if(
    '' != p.long_description,
    p.long_description,
    c.long_description
) as long_description,
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
pr.quantity,
p.weight,
p.active


        ");
        $sqlQuery->addJoin("
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