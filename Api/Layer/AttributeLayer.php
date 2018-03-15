<?php


namespace Module\Ekom\Api\Layer;


use Bat\UriTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class AttributeLayer
{


    public static function getAttributeValueItemsById($attributeId)
    {
        $attributeId = (int)$attributeId;
        return QuickPdo::fetchAll("
select id, concat (id, '. ', label) as label 
from ek_product_attribute_value 
where product_attribute_id=$attributeId         
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getAttributeNamesByShopId($shopId = null)
    {
        $shopId = E::getShopId($shopId);


        return A::cache()->get("", function () use ($shopId) {
            return QuickPdo::fetchAll("
select distinct a.name 
from ek_product_attribute a
inner join ek_product_has_product_attribute h on h.product_attribute_id=a.id 
inner join ek_shop_has_product shp on shp.product_id=h.product_id 
where shp.shop_id=$shopId
and shp.active=1
    ", [], \PDO::FETCH_COLUMN);
        }, [
            "ek_product_attribute",
            "ek_product_has_product_attribute",
            "ek_shop_has_product",
        ]);
    }


    /**
     *
     *
     * Intent:
     * take all products,
     * that have at least an attribute,
     * and owned by the given category,
     * and count the number of products (in that selection)
     * that has the same attribute and value combination.
     *
     *
     *
     *
     *
     * The result of this method answers the question:
     *
     * What are the attributes inside the given category and
     * count: how many products have this attribute.
     *
     *
     */
    public static function getAvailableAttributeByCategoryId($categoryId, $shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        return A::cache()->get("Ekom.AttributeLayer.getAvailableAttributeByCategoryId.$shopId.$langId.$categoryId", function () use ($categoryId, $langId, $shopId) {
            $catIds = CategoryLayer::getSelfAndChildrenIdsById($categoryId, $shopId, $langId);

            $rows = QuickPdo::fetchAll("
select 
#p.id as product_id,
a.name,
al.name as name_label,
v.value,
vl.value as value_label,
a.id as attribute_id,
v.id as value_id,
count(distinct p.id) as count
        
from ek_category_has_product_card chc 
inner join ek_product_card c on c.id=chc.product_card_id
inner join ek_product p on p.product_card_id=c.id
inner join ek_product_has_product_attribute h on h.product_id=p.id
inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_lang al on al.product_attribute_id=a.id
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id
inner join ek_product_attribute_value_lang vl on vl.product_attribute_value_id=v.id

inner join ek_shop_has_product_card shc on shc.product_card_id=c.id
inner join ek_shop_has_product shp on shp.product_id=p.id

        
where chc.category_id in(" . implode(', ', $catIds) . ")        
and al.lang_id=$langId
and shc.shop_id=$shopId        
and shc.active=1        
and shp.active=1        


group by a.name, v.value
        
        ");


            return $rows;
//            $ret = [];
//            foreach ($rows as $row) {
//                $row['uri'] = UriTool::uri(null, [
//                    $row['name'] => $row['value'],
//                ], false, false);
//                $ret[] = $row;
//            }
//            return $ret;

        }, [
            "ek_category_has_product_card",
            "ek_product_card",
            "ek_product",
            "ek_product_has_product_attribute",
            "ek_product_attribute",
            "ek_product_attribute_lang",
            "ek_product_attribute_value",
            "ek_product_attribute_value_lang",
            "ek_shop_has_product_card",
        ]);
    }


    public function insertAttributeIfNotExist($name)
    {

        $id = QuickPdo::fetch("select id from ek_product_attribute where name = :name", [
            "name" => $name,
        ], \PDO::FETCH_COLUMN);
        if (false === $id) {
            $id = EkomApi::inst()->productAttribute()->create([
                "name" => $name,
            ]);
        }
        return (int)$id;
    }


    public function insertAttributeValueIfNotExist($value)
    {

        $id = QuickPdo::fetch("select id from ek_product_attribute_value where value = :value", [
            "value" => $value,
        ], \PDO::FETCH_COLUMN);
        if (false === $id) {
            $id = EkomApi::inst()->productAttributeValue()->create([
                "value" => $value,
            ]);
        }
        return (int)$id;
    }


    public function getAttributeCombinationByProductId($pId)
    {
        $pId = (int)$pId;
        return A::cache()->get("Ekom.AttributeLayer.getAttributeCombinationByProductId.$pId", function () use ($pId) {

            return QuickPdo::fetchAll("
select distinct product_attribute_id
from ek_product_has_product_attribute
where product_id=$pId
", [], \PDO::FETCH_COLUMN);
        }, [
            "ek_product_has_product_attribute",
        ]);
    }

//    public function getAttrValueBySlug($slug)
//    {
//        return A::cache()->get("Ekom.AttributeLayer.getAttrValueBySlug.$slug", function () use ($slug) {
//            return QuickPdo::fetch("select id from ek_product_attribute_value where `value`=:slug", [
//                'slug' => $slug,
//            ], \PDO::FETCH_COLUMN);
//        }, [
//            "ek_product_attribute_value",
//        ]);
//    }


    public function getAvailableAttributeByCategorySlug($categorySlug, $groupByAttribute = true, $shopId = null, $langId = null)
    {
        $categoryId = EkomApi::inst()->categoryLayer()->getIdBySlug($categorySlug);
        $ret = $this->getAvailableAttributeByCategoryId($categoryId, $shopId, $langId);
        if (true === $groupByAttribute) {
            $ret2 = [];
            foreach ($ret as $p) {
                $name = $p['name'];
                $ret2[$name][] = $p;
            }
            return $ret2;
        }
        return $ret;
    }


}