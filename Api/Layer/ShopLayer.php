<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\Exception\EkomApiException;
use QuickPdo\QuickPdo;

class ShopLayer
{


    /**
     * @return array, the adminitrative address for this shop.
     * @see EkomModels::shopPhysicalAddress()
     * @throws EkomApiException
     */
    public static function getAddress()
    {

        $row = QuickPdo::fetch("
select 
a.*,
c.iso_code as country_iso_code,
c.label as country

from ek_shop s 
inner join ek_address a on a.id=s.address_id 
inner join ek_country c on c.id=a.country_id 

limit 0, 1        
        ");
        if (false !== $row) {
            return $row;
        }
        throw new EkomApiException("Address not found: please set an administrative for your shop now!!!");
    }


}
