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


    public static function getIdByIsoCode($isoCode)
    {
        return QuickPdo::fetch("
select id 
from ek_currency
where iso_code=:iso
", [
            "iso" => $isoCode,
        ], \PDO::FETCH_COLUMN);
    }
}