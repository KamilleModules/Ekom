<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class AddressLayer
{

    public static function getEntries()
    {
        return QuickPdo::fetchAll('
select a.id,
concat(
    a.first_name,
    " ", 
    a.last_name,
    " - ", 
    a.address,
    " ", 
    a.postcode,
    " ", 
    a.city,
    " ", 
    UPPER(l.label)
) as address

from ek_address a
 
inner join ek_country c on c.id=a.country_id 
inner join ek_country_lang l on l.country_id=c.id 
        
', [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function countEntries()
    {
        return QuickPdo::count('ek_address');
    }

}