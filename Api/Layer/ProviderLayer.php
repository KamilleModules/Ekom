<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class ProviderLayer
{

    public static function getItems($shopId)
    {

        $shopId = (int)$shopId;
        return QuickPdo::fetchAll('
select id, concat (id, ". ", name) as label
from ek_provider where shop_id=' . $shopId, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);


    }
}