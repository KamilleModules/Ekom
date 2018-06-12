<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class ProviderLayer
{

    public static function getName2IdList()
    {
        return QuickPdo::fetchAll("select name, id from ek_provider", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getLabelById(int $id)
    {
        return QuickPdo::fetch("select label from ek_provider where id=$id", [], \PDO::FETCH_COLUMN);
    }

    public static function getItems($shopId)
    {

        $shopId = (int)$shopId;
        return QuickPdo::fetchAll('
select id, concat (id, ". ", name) as label
from ek_provider where shop_id=' . $shopId, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);


    }
}