<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ManufacturerLayer
{




    public static function getItemsList(array $options = [])
    {
        $alphaSort = $options['alphaSort'] ?? false;
        $q = "select id, name from ek_manufacturer";
        if ($alphaSort) {
            $q .= " order by name asc";
        }
        return QuickPdo::fetchAll($q, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }



    public static function getItems($shopId)
    {
        return QuickPdo::fetchAll("
select id, name from ek_manufacturer 
where shop_id=$shopId
order by name asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

}