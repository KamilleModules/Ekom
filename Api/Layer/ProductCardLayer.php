<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\ListModifiers;
use QuickPdo\QuickPdo;

class ProductCardLayer
{


//    public function getProductCardsByCategory($categoryId)
//    {
//
//        EkomApi::inst()->initWebContext();
//        $langId = (int)ApplicationRegistry::get("ekom.lang_id");
//
//
//        /**
//         * Todo: cache;
//         */
//        $catIds = EkomApi::inst()->categoryLayer()->getDescendantCategoryIdTree($categoryId);
//
//        return QuickPdo::fetchAll("
//select
//l.product_card_id,
//l.label
//
//from ek_product_card_lang l
//inner join ek_category_has_product_card h on h.product_card_id=l.product_card_id
//
//where h.category_id in (" . implode(', ', $catIds) . ")
//and l.lang_id=$langId
//
//
//        ");
//    }


    public function getProductCardsByCategory($categoryId, ListModifiers $filter = null, $shopId = null, $langId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? (int)ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? (int)ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        $categoryId = (int)$categoryId;
        $catIds = EkomApi::inst()->categoryLayer()->getDescendantCategoryIdTree($categoryId);


        return A::cache()->get("Ekom.ProductCardLayer.getProductCardsByCategory.$shopId.$langId.$categoryId.$filter", function () use ($catIds, $langId, $shopId) {

            $rows = QuickPdo::fetchAll("
select 

shc.product_id as product_id,
c.id as card_id
        
        
        
from ek_category_has_product_card chc 
inner join ek_product_card c on c.id=chc.product_card_id
inner join ek_product p on p.product_card_id=c.id



inner join ek_product_has_product_attribute h on h.product_id=p.id
inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id


inner join ek_shop_has_product_card shc on shc.product_card_id=c.id and shc.product_id is not null
inner join ek_shop_has_product shp on shp.product_id=p.id

        
where chc.category_id in(" . implode(', ', $catIds) . ")        
and shc.shop_id=$shopId        
and shc.active=1        
and shp.active=1        

group by c.id
        
        ");
            $ret = [];
            $productLayer = EkomApi::inst()->productLayer();
            foreach ($rows as $row) {
                $ret[] = $productLayer->getProductBoxModelByCardId($row['card_id'], null, null, $row['product_id']);
            }
            return $ret;

        }, [
            "ek_category_has_product_card",
            "ek_product_card",
            "ek_product",
            "ek_product_has_product_attribute",
            "ek_product_attribute",
            "ek_product_attribute_value",
            "ek_shop_has_product_card.create",
            "ek_shop_has_product_card.delete.$shopId",
            "ek_shop_has_product_card.update.$shopId",
        ]);
    }


}