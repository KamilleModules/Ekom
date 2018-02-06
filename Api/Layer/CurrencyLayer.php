<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class CurrencyLayer
{


    /**
     * @param $id
     * @return array|false
     *      - iso_code
     *      - exchange_rate
     */
    public static function getCurrencyInfoById($id, $shopId)
    {
        $id = (int)$id;
        $shopId = (int)$shopId;
        return QuickPdo::fetch("
select 
c.iso_code, 
h.exchange_rate 
from ek_currency c 
inner join ek_shop_has_currency h on h.currency_id=c.id
where c.id=$id
and h.shop_id=$shopId

");

    }


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

    public static function getCurrencyEntries()
    {
        return QuickPdo::fetchAll("
select *
from ek_currency
");
    }


    public static function getCurrencyEntriesByShopId($shopId)
    {
        $shopId = (int)$shopId;

        return QuickPdo::fetchAll("
select *
from ek_currency c 
inner join ek_shop_has_currency h on h.currency_id=c.id 
where h.shop_id=$shopId
");

    }

    public static function getCurrencyItems()
    {
        return QuickPdo::fetchAll("
select id, iso_code
from ek_currency
", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);

    }
}