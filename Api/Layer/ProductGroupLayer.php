<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ProductGroupLayer
{


    public static function getRelatedCardIdByGroupName(string $groupName)
    {
        return QuickPdo::fetchAll("
select 
p.product_card_id
from ek_product_group g 
inner join ek_product_group_has_product h on h.product_group_id=g.id
inner join ek_product p on p.id=h.product_id
order by h.order asc
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getNameById(int $id)
    {
        return QuickPdo::fetch("select name from ek_product_group where id=$id", [], \PDO::FETCH_COLUMN);
    }

    public static function getReferenceByGroupId($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("
select name from ek_product_group where id=$id        
        ", [], \PDO::FETCH_COLUMN);


    }


    public static function getAllGroupNames()
    {
        return QuickPdo::fetchAll("
select name from ek_product_group        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getProductIdsByGroup($groupName)
    {
        return A::cache()->get("Ekom.ProductGroupLayer.getProductIdsByGroup.$groupName", function () use ($groupName) {
            return QuickPdo::fetchAll("
select h.product_id from ek_product_group_has_product h
inner join ek_product_group pg on pg.id=h.product_group_id
where pg.name=:name
", [
                'name' => $groupName,
            ], \PDO::FETCH_COLUMN);
        });
    }
}



