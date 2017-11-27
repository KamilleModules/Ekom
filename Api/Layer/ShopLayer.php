<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
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


    public static function getShopItemById($shopId)
    {
        $shopId = (int)$shopId;
        return QuickPdo::fetch("
select * from ek_shop where id=$shopId        
        ");
    }

    public static function getShopInfoById($shopId)
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


    public static function getShopEntries()
    {
        return QuickPdo::fetchAll("
select * from ek_shop order by id asc        
        ");
    }

    /**
     * Return the closest shop physical address, given an user shipping address.
     * Or false if the shop is virtual (no physical address).
     *
     *
     * @param $shippingAddress array|null, an addressModel as defined in UserAddressLayer,
     *                          or null if the user has no shipping address (or he/she is not connected)
     * @return array|null, a physical address as described at the top of this class.
     *                  Or return null if the shop contains no physical address at all.
     */
    public static function getClosestPhysicalAddress(array $shippingAddress = null)
    {

        $shopAddresses = self::getPhysicalAddresses();
        if ($shopAddresses) {
            if (null !== $shippingAddress) {

                /**
                 * @var $estimator DistanceEstimatorInterface
                 */
                $estimator = X::get("Ekom_DistanceEstimator");
                $closest = false;
                $distance = 30000; // impossible to reach distance
                foreach ($shopAddresses as $shopAddress) {
                    $distanceToUserCountry = $estimator->estimate($shopAddress, $shippingAddress);
                    if ($distanceToUserCountry < $distance) {
                        $distance = $distanceToUserCountry;
                        $closest = $shopAddress;
                    }
                }
                return $closest;
            } else {
                return array_shift($shopAddresses);
            }
        }
        return null;

    }


    public static function getPhysicalAddresses($type = null, $shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);


        return A::cache()->get("Ekom.ShopLayer.getPhysicalAddresses.$shopId.$langId.$type", function () use ($shopId, $langId, $type) {

            $sType = "";
            $markers = [];
            if (null !== $type) {
                $sType = "and h.type=:the_type";
                $markers['the_type'] = $type;
            }

            $q = "
            select 
a.*,
c.iso_code as country_iso_code,
l.label as country

from ek_shop_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country c on c.id=a.country_id 
inner join ek_country_lang l on l.country_id=c.id 

where h.shop_id=$shopId
$sType
and a.active=1
and l.lang_id=$langId

order by h.`order` asc
            ";


            return QuickPdo::fetchAll($q, $markers);

        }, [
            "ek_shop_has_address",
        ]);
    }

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
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

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


    public function allIds()
    {
        return QuickPdo::fetchAll("select id from ek_shop", [], \PDO::FETCH_COLUMN);
    }


    public function getLangIds($shopId)
    {
        $shopId = (int)$shopId;
        return A::cache()->get("Ekom.ShopLayer.getLangIds.$shopId", function () use ($shopId) {
            return QuickPdo::fetchAll("
select lang_id from ek_shop_has_lang where shop_id=$shopId        
        ", [], \PDO::FETCH_COLUMN);
        }, [
            "ek_shop_has_lang",
            "ek_shop.delete",
            "ek_lang.delete",
        ]);
    }

}