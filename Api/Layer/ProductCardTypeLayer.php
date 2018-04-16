<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\Exception\EkomApiException;
use QuickPdo\QuickPdo;

class ProductCardTypeLayer
{
    public static function getIdByName(string $name)
    {
        return QuickPdo::fetch("select id from ek_product_card_type where name=:name", [
            "name" => $name,
        ], \PDO::FETCH_COLUMN);
    }

    public static function getListItems()
    {
        return QuickPdo::fetchAll("
select id, label from ek_product_card_type        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getInfoByProductCardId(int $cardId)
    {
        return QuickPdo::fetch("
select t.* from ek_product_card_type t 
inner join ek_product_card c on c.product_card_type_id=t.id
where c.id=$cardId
");
    }


    public static function getDefaultTypeInfo()
    {
        $row = QuickPdo::fetch("
select * from ek_product_card_type 
where `name`= :name
", [
            "name" => "default",
        ]);
        if (false === $row) {
            throw new EkomApiException("Please set a product_card_type with name=default to start");
        }
        return $row;
    }

}