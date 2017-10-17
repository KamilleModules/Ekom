<?php


namespace Module\Ekom\Api\Maintenance;


use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ProductHelper
{


    public static function fillEmptyShopReferences($shopId)
    {
        $shopId = E::getShopId($shopId);
        QuickPdo::freeQuery("
update 
ek_shop_has_product shp
inner join ek_product p on p.id=shp.product_id

set shp.reference=p.reference   

where 
shp.shop_id=$shopId 
and shp.reference=''
     
        ");
    }

}