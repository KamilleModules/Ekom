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


    public static function getShopInfoModel($shopAddressId = null)
    {
        $shopInfo = [];
        if (null !== $shopAddressId) {
            $shopAddress = ShopLayer::getPhysicalAddressById($shopAddressId);
        } else {
            $shopAddress = ShopLayer::getDefaultShopAddress();
        }
        $shopInfo['address'] = $shopAddress;
        return $shopInfo;
    }



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






}