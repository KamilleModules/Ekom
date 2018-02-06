<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class ProductAttributeLayer
{


    public static function getNameById($productAttributeId)
    {
        $productAttributeId = (int)$productAttributeId;
        return QuickPdo::fetch("
select name from ek_product_attribute where id=$productAttributeId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getProductAttributeValueById($productAttributeValueId)
    {
        $productAttributeValueId = (int)$productAttributeValueId;
        return QuickPdo::fetch("
select value from ek_product_attribute_value where id=$productAttributeValueId        
        ", [], \PDO::FETCH_COLUMN);
    }


    public static function getProductAttributeItems()
    {
        return QuickPdo::fetchAll("
select id, name from ek_product_attribute order by name asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getProductAttributeValueItems()
    {
        return QuickPdo::fetchAll("
select id, value from ek_product_attribute_value order by value asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

}