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


        return A::cache()->get("Module.Ekom.Api.Layer.ShopLayer.getShopPhysicalAddress.$shopId.$langId", function () use ($shopId, $langId) {

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

        }, []);
    }

}