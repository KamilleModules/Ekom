<?php


namespace Module\Ekom\Api\Entity;


use Bat\HashTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use QuickPdo\QuickPdo;

class ProductBoxEntityUtil
{


    /**
     * @deprecated
     */
    public static function getMergedProductDetails(array $productDetailsArr)
    {
        $majorDetailsParams = (array_key_exists('major', $productDetailsArr)) ? $productDetailsArr['major'] : [];
        $minorDetailsParams = (array_key_exists('minor', $productDetailsArr)) ? $productDetailsArr['minor'] : [];
        return array_merge($majorDetailsParams, $minorDetailsParams);
    }


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


    public static function setProductBoxGeneralContext(array $gpc)
    {
        ApplicationRegistry::set("ekom.gpc", $gpc);
    }

    public static function getProductCardInfoByCardId($cardId)
    {
        $cardId = (int)$cardId;

        return A::cache()->get("Ekom.ProductBoxEntityUtil.getProductCardInfoByCardId.$cardId", function () use ($cardId) {

            /**
             * First get the product card info
             */
            if (false !== ($row = QuickPdo::fetch("
select *
from ek_product_card 
where id=$cardId  
"))
            ) {
                return $row;
            }
            return false;
        });
    }


    public static function getProductCardProductsWithAttributes($cardId)
    {
        $cardId = (int)$cardId;


        return A::cache()->get("Ekom.ProductBoxEntityUtil.getProductCardProductsWithAttributes.$cardId", function () use ($cardId) {


            $productsInfo = QuickPdo::fetchAll("
select p.id as product_id, pr.quantity, p.active 
from ek_product p 
inner join ek_product_reference pr on pr.product_id=p.id
where product_card_id=$cardId        
        ");

            $productIds = [];
            foreach ($productsInfo as $row) {
                $productIds[] = $row['product_id'];
            }

            if ($productIds) {


                $rows = QuickPdo::fetchAll("
select 
h.product_id,
a.id as attribute_id,
a.label as attribute_label,
a.name,
v.value,
v.id as value_id,
v.label as value_label

from ek_product_has_product_attribute h
inner join ek_product p on p.id=h.product_id
inner join ek_product_attribute a on a.id=h.product_attribute_id 
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id
inner join ek_product_card_has_product_attribute hh on hh.product_card_id=p.product_card_id 
  

where product_id in (" . implode(', ', $productIds) . ")
         
order by hh.order asc         
         
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

        });
    }


    public static function getProductCardProducts($cardId)
    {
        $cardId = (int)$cardId;

        return A::cache()->get("Ekom.ProductBoxEntityUtil.getProductCardProducts.$cardId", function () use ($cardId) {


            $q = "
select 
p.id as product_id,
p.reference,
p.weight,
p.price,
t.name as product_card_type_name,
t.label as product_card_type_label,
t.id as product_card_type_id,
p.quantity,
p.active,
p.codes,
p._popularity,

p.wholesale_price,
se.name as seller,
se.label as seller_label,

p.label,
p.description,
p.slug,
p.meta_title,
p.meta_description,
p.meta_keywords,
p.out_of_stock_text


from ek_product p 
inner join ek_product_card c on c.id=p.product_card_id
inner join ek_product_card_type t on t.id=c.product_card_type_id
inner join ek_seller se on se.id=p.seller_id

where 
p.product_card_id=$cardId


        ";
            $productRows = QuickPdo::fetchAll($q);


            return $productRows;
        });
    }
}













