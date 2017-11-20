<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ProductGroupLayer
{

    public static function getAllGroupNames($shopId = null)
    {
        $shopId = E::getShopId($shopId);
        return QuickPdo::fetchAll("
select name from ek_product_group where shop_id=$shopId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getProductIdsByGroup($groupName, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        return A::cache()->get("Ekom.ProductGroupLayer.getProductIdsByGroup.$shopId.$groupName", function () use ($groupName, $shopId) {
            return QuickPdo::fetchAll("
select h.product_id from ek_product_group_has_product h
inner join ek_product_group pg on pg.id=h.product_group_id
where pg.name=:name
and pg.shop_id=$shopId
", [
                'name' => $groupName,
            ], \PDO::FETCH_COLUMN);
        });
    }
}



