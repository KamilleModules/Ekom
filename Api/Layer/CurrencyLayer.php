<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class CurrencyLayer
{

    /**
     * @param $id
     * @return string|false
     */
    public static function getIsoCodeById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("
select iso_code 
from ek_currency
where id=$id
", [], \PDO::FETCH_COLUMN);
    }
}