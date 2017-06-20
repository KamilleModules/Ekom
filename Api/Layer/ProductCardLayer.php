<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
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


    public function getAvailableAttributeByCategoryId($categoryId, $shopId = null, $langId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? (int)ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? (int)ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

        $catIds = EkomApi::inst()->categoryLayer()->getDescendantCategoryIdTree($categoryId);


        return QuickPdo::fetchAll("
select 
a.name,
v.value,
count(v.value) as count
        
from ek_category_has_product_card chc 
inner join ek_product_card c on c.id=chc.product_card_id
inner join ek_product p on p.product_card_id=c.id
inner join ek_product_has_product_attribute h on h.product_id=p.id
inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_lang al on al.product_attribute_id=a.id
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id
inner join ek_product_attribute_value_lang vl on vl.product_attribute_value_id=v.id

inner join ek_shop_has_product_card shc on shc.product_card_id=c.id

        
where chc.category_id in(" . implode(', ', $catIds) . ")        
and al.lang_id=$langId
and shc.shop_id=$shopId        
and shc.active=1        


group by a.name, v.value
        
        ");
    }

}