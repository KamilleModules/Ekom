<?php


namespace Module\Ekom\Api\Entity;


use Bat\HashTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use QuickPdo\QuickPdo;

class ProductBoxEntityUtil
{

    public static function filterProductDetails(array $pool)
    {
        $availableProductDetails = [];
        Hooks::call("Ekom_ProductBox_collectAvailableProductDetails", $availableProductDetails);
        return array_intersect_key($pool, array_flip($availableProductDetails));
    }

    public static function hashify($string)
    {
        $gpc = ProductBoxEntityUtil::getProductBoxGeneralContext();
        $hash = ProductBoxEntityUtil::getHashByCacheContext($gpc);
        return $string . "-$hash";
    }

    public static function getHashByCacheContext(array $context)
    {
        return HashTool::getHashByArray($context);
    }

    /**
     * @return array, return general product context, used by lists of product boxes and product boxes.
     */
    public static function getProductBoxGeneralContext()
    {
        $gpc = ApplicationRegistry::get("ekom.gpc");
        if (null === $gpc) {
            $gpc = [];
            Hooks::call("Ekom_ProductBox_collectGeneralContext", $gpc);
        }
        return $gpc;
    }

    public static function getProductCardInfoByCardId($cardId, $shopId, $langId)
    {
        $cardId = (int)$cardId;
        $shopId = (int)$shopId;
        $langId = (int)$langId;

        return A::cache()->get("Ekom.ProductBoxEntityUtil.getProductCardInfoByCardId.$langId.$shopId.$cardId", function () use ($shopId, $langId, $cardId) {

            /**
             * First get the product card info
             */
            if (false !== ($row = QuickPdo::fetch("
select
 
sl.label,
sl.slug,
sl.description,
sl.meta_title,
sl.meta_description,
sl.meta_keywords,
s.product_id,
s.active,
l.label as default_label,
l.description as default_description,
l.meta_title as default_meta_title,
l.meta_description as default_meta_description,
l.meta_keywords as default_meta_keywords,
l.slug as default_slug

from ek_shop_has_product_card_lang sl 
inner join ek_shop_has_product_card s on s.shop_id=sl.shop_id and s.product_card_id=sl.product_card_id
inner join ek_product_card_lang l on l.product_card_id=sl.product_card_id and l.lang_id=sl.lang_id

where s.shop_id=$shopId 
and s.product_card_id=$cardId and sl.lang_id=$langId 
"))
            ) {
                return $row;
            }
            return false;
        }, [
            "ek_shop_has_product_card_lang",
            "ek_shop_has_product_card",
            "ek_product_card_lang",
        ]);
    }


    public static function getProductCardProductsWithAttributes($cardId, $shopId, $langId)
    {
        $cardId = (int)$cardId;
        $shopId = (int)$shopId;
        $langId = (int)$langId;


        return A::cache()->get("Ekom.ProductBoxEntityUtil.getProductCardProductsWithAttributes.$shopId.$langId.$cardId", function () use ($shopId, $langId, $cardId) {

            $productsInfo = self::getProductCardProducts($cardId, $shopId, $langId);

            $productIds = [];
            foreach ($productsInfo as $row) {
                $productIds[] = $row['product_id'];
            }

            if ($productIds) {


                $rows = QuickPdo::fetchAll("
select 
h.product_id,
al.product_attribute_id as attribute_id,
al.name as name_label,
a.name,
v.value,
vl.product_attribute_value_id as value_id,
vl.value as value_label

from ek_product_has_product_attribute h
inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_lang al on al.product_attribute_id=a.id 
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id 
inner join ek_product_attribute_value_lang vl on vl.product_attribute_value_id=v.id 

where al.lang_id=$langId 
and vl.lang_id=$langId
and product_id in (" . implode(', ', $productIds) . ")
         
order by h.order asc         
         
");


                $productId2attr = [];

                foreach ($rows as $row) {
                    $pid = $row['product_id'];
                    unset($row['product_id']);
                    $productId2attr[$pid][] = $row;
                }

                foreach ($productsInfo as $k => $row) {
                    $pid = $row['product_id'];
                    if (array_key_exists($pid, $productId2attr)) {
                        $productsInfo[$k]['attributes'] = $productId2attr[$pid];
                    } else {
//                    XLog::error("[Ekom module] - ProductLayer: attributes not found for product with id $pid in shop $shopId and lang $langId");
                        $productsInfo[$k]['attributes'] = [];
                    }
                }
                return $productsInfo;
            }
            return [];

        }, [
            "ek_product_has_product_attribute",
            "ek_product_attribute_lang",
            "ek_product_attribute_value_lang",
            "ek_product",
        ]);
    }


    public static function getProductCardProducts($cardId, $shopId, $langId)
    {
        $cardId = (int)$cardId;
        $shopId = (int)$shopId;
        $langId = (int)$langId;

        return A::cache()->get("Ekom.ProductBoxEntityUtil.getProductCardProducts.$shopId.$langId.$cardId", function () use ($cardId, $shopId, $langId) {


            $productRows = QuickPdo::fetchAll("
select 
p.id as product_id,
p.reference,
p.weight,
COALESCE (s.price, p.price) as price,
t.name as product_type,
s.quantity,
s.active,
s.codes,
se.name as seller,
l.label,
l.description,
l.meta_title,
l.meta_description,
l.meta_keywords,
l.out_of_stock_text,
ll.label as default_label,
ll.description as default_description,
ll.meta_title as default_meta_title,
ll.meta_description as default_meta_description,
ll.meta_keywords as default_meta_keywords,
l.slug


from ek_product p
inner join ek_product_lang ll on ll.product_id=p.id
inner join ek_shop_has_product s on s.product_id=p.id 
inner join ek_product_type t on t.id=s.product_type_id
inner join ek_shop_has_product_lang l on l.shop_id=s.shop_id and l.product_id=s.product_id
inner join ek_seller se on se.id=s.seller_id

where 
l.lang_id=$langId
and ll.lang_id=$langId
and s.shop_id=$shopId
and p.product_card_id=$cardId


        ");


            return $productRows;
        }, [
            "ek_product",
            "ek_shop_has_product",
            "ek_shop_has_product_lang",
            "ek_seller",
        ]);
    }
}













