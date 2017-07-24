<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class ShopLayer
{


    public function getShopInfoByHost($host)
    {

        return QuickPdo::fetch('select 
s.id as shop_id,        
s.lang_id,        
s.currency_id,        
t.name as timezone
from ek_shop s 
inner join ek_timezone t on t.id=s.timezone_id
where s.host=:host
        
        ', [
            'host' => $host,
        ]);
    }

    public function getShopPhysicalAddress($shopId = null, $langId = null)
    {


        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? (int)ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? (int)ApplicationRegistry::get("ekom.lang_id") : (int)$langId;


        return A::cache()->get("Ekom.ShopLayer.getShopPhysicalAddress.$shopId.$langId", function () use ($shopId, $langId) {

            return QuickPdo::fetch(" 
select 
a.city,
a.postcode,
a.address,
a.country_id,
l.label as country

from ek_shop_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country_lang l on l.country_id=a.country_id 

where h.shop_id=$shopId
and h.type='physical'
and a.active=1
and l.lang_id=$langId

order by h.`order` asc
        
        ");

        }, [
            "ek_shop_has_address.create",
            "ek_shop_has_address.delete.$shopId",
            "ek_shop_has_address.update.$shopId",
        ]);
    }


    public function getShopInfoById($shopId)
    {
        $shopId = (int)$shopId;

        return A::cache()->get("Ekom.ShopLayer.getShopInfoById.$shopId", function () use ($shopId) {


            return QuickPdo::fetch("
select s.*,
c.iso_code,
h.exchange_rate,
t.name as timezone

from ek_shop s
inner join ek_shop_has_currency h on h.shop_id=s.id
inner join ek_currency c on c.id=h.currency_id
inner join ek_timezone t on t.id=s.timezone_id

where s.id=$shopId
");

        }, [
            "ek_shop.create",
            "ek_shop.delete.$shopId",
            "ek_shop.update.$shopId",
            "ek_shop_has_currency.create",
            "ek_shop_has_currency.delete.$shopId",
            "ek_shop_has_currency.update.$shopId",
        ]);
    }


    public function allIds(){
        return QuickPdo::fetchAll("select id from ek_shop", [], \PDO::FETCH_COLUMN);
    }


}