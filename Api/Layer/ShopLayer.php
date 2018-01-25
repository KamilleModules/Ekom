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

    public static function setBaseCurrency($shopId, $currencyId)
    {
        // get the current base currency.
        $shopId = (int)$shopId;
        $currencyId = (int)$currencyId;


        QuickPdo::transaction(function () use($shopId, $currencyId){


            $newExchangeRate = QuickPdo::fetch("select exchange_rate from ek_shop_has_currency 
where shop_id=$shopId and currency_id=$currencyId", [], \PDO::FETCH_COLUMN);
            if (false === $newExchangeRate) {
                throw new EkomException("Case not handled yet, maybe for a quick fix try to set the shop.base_currency_id manually.");
            }

            // update all exchange_rate
            QuickPdo::freeExec("
update ek_shop_has_currency set exchange_rate = exchange_rate / $newExchangeRate 
where shop_id=$shopId       
        ");

            QuickPdo::update("ek_shop", [
                "base_currency_id" => $currencyId,
            ], [
                ["id", "=", $shopId],
            ]);
        });


    }

    public static function getCurrencyIsoCodes($shopId)
    {
        return QuickPdo::fetchAll("
select c.id, c.iso_code  
from ek_currency c 
inner join ek_shop_has_currency h on h.currency_id=c.id
where h.shop_id=$shopId         
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getLangIsoCodes($shopId)
    {
        return QuickPdo::fetchAll("
select l.id, l.iso_code  
from ek_lang l 
inner join ek_shop_has_lang h on h.lang_id=l.id
where h.shop_id=$shopId         
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    /**
     * @param null $shopId
     * @param null $langId
     * @return array:shopPhysicalAddress
     * @see EkomModels::shopPhysicalAddress()
     * @throws EkomException
     */
    public static function getDefaultShopAddress($shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $addresses = self::getPhysicalAddresses(null, $shopId, $langId);
        if ($addresses) {
            $row = array_shift($addresses);
            return $row;
        }
        throw new EkomException("This shop has no default address: $shopId");
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
    public static function getPhysicalAddressById($addressId, $shopId, $langId = null)
    {
        $addresses = self::getPhysicalAddresses(null, $shopId, $langId);
        $addressId = (int)$addressId;
        foreach ($addresses as $address) {
            if ($addressId === (int)$address['id']) {
                return $address;
            }
        }
        throw new EkomException("The address was not found with addressId: $addressId, shopId: $shopId, langId: $langId");
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

        });
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