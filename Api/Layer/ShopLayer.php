<?php


namespace Module\Ekom\Api\Layer;


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

}