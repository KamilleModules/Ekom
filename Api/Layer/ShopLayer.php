<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Model\EkomModel;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\DistanceEstimator\DistanceEstimatorInterface;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 *
 * shopPhysicalAddress
 * --------------------
 * @see EkomModels::shopPhysicalAddress()
 *
 *
 */
class ShopLayer
{





    //--------------------------------------------
    // OLD
    //--------------------------------------------
    /**
     * @param null $shopId
     * @param null $langId
     * @return array:shopPhysicalAddress
     * @see EkomModels::shopPhysicalAddress()
     * @throws EkomException
     */
    public static function getDefaultShopAddress()
    {
        $addresses = self::getPhysicalAddresses();
        if ($addresses) {
            $row = array_shift($addresses);
            return $row;
        }
        throw new EkomException("This shop has no default address");
    }

    public static function getShopItemById($shopId)
    {
        $shopId = (int)$shopId;
        return QuickPdo::fetch("
select * from ek_shop where id=$shopId        
        ");
    }

    public static function getHostById($shopId)
    {
        $shopId = (int)$shopId;
        return QuickPdo::fetch("select host from ek_shop where id=$shopId", [], \PDO::FETCH_COLUMN);
    }

    public static function getShopInfoById($shopId)
    {
        $shopId = (int)$shopId;

        return A::cache()->get("Ekom.ShopLayer.getShopInfoById.$shopId", function () use ($shopId) {


            return QuickPdo::fetch("
select s.*,
c.iso_code as currency_iso_code,
h.exchange_rate as currency_exchange_rate,
t.name as timezone

from ek_shop s
inner join ek_shop_has_currency h on h.shop_id=s.id
inner join ek_currency c on c.id=h.currency_id
inner join ek_timezone t on t.id=s.timezone_id

where s.id=$shopId
");

        });
    }


    public static function getShopEntries()
    {
        return QuickPdo::fetchAll("
select * from ek_shop order by id asc        
        ");
    }


    /**
     * Return the shop physical address for the given address id.
     *
     * @param $addressId
     * @param $shopId
     * @param null $langId
     *
     * @return array:shopPhysicalAddress
     * @see EkomModels::shopPhysicalAddress()
     * @throws EkomException
     */
    public static function getPhysicalAddressById($addressId)
    {
        $addresses = self::getPhysicalAddresses(null);
        $addressId = (int)$addressId;
        foreach ($addresses as $address) {
            if ($addressId === (int)$address['id']) {
                return $address;
            }
        }
        throw new EkomException("This shop doesn't have an address with id: $addressId");
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

        return A::cache()->get("Ekom.ShopLayer.getPhysicalAddresses", function ()  {


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


    public function getShopPhysicalAddress()
    {
        return A::cache()->get("Ekom.ShopLayer.getShopPhysicalAddress", function ()  {

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