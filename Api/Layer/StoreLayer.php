<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use QuickPdo\QuickPdo;

class StoreLayer
{

    public static function getInfoByLabel(string $label)
    {
        return QuickPdo::fetch("
select         

s.label,
a.id,
a.libelle,
a.phone,
a.address,
a.city,
a.postcode,
a.supplement,
a.active,
a.country_id,
c.iso_code as country

from ek_store s 
inner join ek_address a on a.id=s.address_id
inner join ek_country c on c.id=a.country_id

where s.label=:label
        ", [
            "label" => $label,
        ]);
    }


    public static function getPreferredPhysicalAddressById(int $addressId = null)
    {
        $addresses = self::getPhysicalAddresses();
        foreach ($addresses as $address) {
            if ((int)$address['id'] === $addressId) {
                return $address;
            }
        }
        return array_shift($addresses);
    }


    /**
     * @param int|null $addressId
     * @return null|int
     */
    public static function getPreferredPhysicalAddressIdById(int $addressId = null)
    {
        $preferredAddress = self::getPreferredPhysicalAddressById($addressId);
        if ($preferredAddress) {
            return (int)$preferredAddress['id'];
        }
        return null;
    }

    public static function getPhysicalAddressById(int $addressId)
    {


        $q = "
            select 
a.*,
c.iso_code as country_iso_code,
c.label as country

from ek_store h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country c on c.id=a.country_id 

where 
a.id=$addressId
and a.active=1
";


        return QuickPdo::fetch($q);
    }

    public static function hasStoreByLabel(string $label)
    {
        $res = QuickPdo::fetch("
select id from ek_store where label=:label        
        ", [
            "label" => $label,
        ]);
        if (false !== $res) {
            return true;
        }
        return false;
    }


    /**
     * @param null $type , the type of physical address.
     *              By default, if it's null (or empty in the database),
     *              it represents a warehouse address, meaning that the address is used for shipping cost calculations.
     *              As for now, this is the only type of shop address ekom uses, but we can imagine that other types
     *              might exist, such as administrative types.
     *
     * @param null $shopId
     * @param null $langId
     * @return mixed
     */
    public static function getPhysicalAddresses()
    {

        return A::cache()->get("Ekom.StoreLayer.getPhysicalAddresses", function () {


            $markers = [];

            $q = "
            select 
a.*,
c.iso_code as country_iso_code,
c.label as country

from ek_store h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country c on c.id=a.country_id 

where a.active=1";


            return QuickPdo::fetchAll($q, $markers);

        });
    }


    public function getStorePhysicalAddress()
    {
        return A::cache()->get("Ekom.ShopLayer.getShopPhysicalAddress", function () {

            return QuickPdo::fetch(" 
select 
a.city,
a.postcode,
a.address,
a.country_id,
c.label as country

from ek_store h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country c on c.id=a.country_id 

where a.active=1        
        ");

        });
    }

}
