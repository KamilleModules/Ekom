<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\ArrayTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SaveOrmObject\Object\Ek\AddressObject;


/**
 *
 * addressModel
 * -----------------
 * @see EkomModels::addressModel()
 *
 */
class UserAddressLayer
{

    private static $userAddresses = null;

    /**
     * @return null|array:addressModel
     * @see EkomModels::addressModel()
     */
    public static function getPreferredShippingAddress($userId)
    {
        return self::getPreferredAddress($userId, 'shipping');
    }

    /**
     * @return null|array:addressModel
     * @see EkomModels::addressModel()
     */
    public static function getPreferredBillingAddress($userId)
    {
        return self::getPreferredAddress($userId, 'billing');
    }

    /**
     * @return array:addressModel
     * @see EkomModels::addressModel()
     * @throws EkomException
     */
    public static function getAddressById($userId, $addressId)
    {
        $addressId = (int)$addressId;
        $userId = (int)$userId;
        $addresses = self::getUserAddresses($userId, false);
        foreach ($addresses as $addr) {
            if ($addressId === (int)$addr['address_id']) {
                return $addr;
            }
        }
        throw new EkomException("Address not found with userId: $userId, addressId: $addressId");
    }

    /**
     * Return an array of user addresses.
     * Each address is an addressModel
     *
     * @see EkomModels::addressModel()
     *
     */
    public static function getUserAddresses($userId, $activeOnly = true)
    {

        $sActiveOnly = (int)$activeOnly;
        return A::cache()->get("Ekom.UserAddressLayer.getUserAddresses.$userId.$sActiveOnly", function () use ($userId, $activeOnly) {


            $sActive = (true === $activeOnly) ? "and a.active='1'" : "";
            $rows = QuickPdo::fetchAll("
select 
a.id as address_id,        
a.libelle,          
a.phone,              
a.address,        
a.city,        
a.postcode,        
a.supplement,        
c.label as country,
c.id as country_id,
c.iso_code as country_iso_code,
h.is_default_shipping_address,
h.is_default_billing_address


from ek_user_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country c on c.id=a.country_id

where 
h.user_id=$userId 
$sActive

order by h.`order` asc 
        
        
        ");

            // depending on the shop's country?
            foreach ($rows as $k => $row) {


                list($fName, $fAddress) = self::getFormattedNameAndAddress($row);
                $rows[$k]['fName'] = $fName;
                $rows[$k]['fAddress'] = $fAddress;
            }

            return $rows;
        }, true);
    }


    public static function userOwnsAddress($userId, $id)
    {
        $id = (int)$id;
        $userId = (int)$userId;
        if (false !== QuickPdo::fetch("
select user_id from ek_user_has_address
where user_id=$userId 
and address_id=$id
")) {
            return true;
        }
        return false;
    }


    /**
     *
     * This method is used during the checkout phase.
     * See ShippingOrderBuilderStep.
     *
     * @param $billingAddressId
     * @param $shippingAddressId
     * @param null $userId
     * @param null $langId
     * @return array:
     *              - 0: billingAddress
     *              - 1: shippingAddress
     *              - 2: allAddresses
     */
    public function getUserBillingShippingAndAddresses($billingAddressId, $shippingAddressId, $userId = null, $langId = null)
    {

        $addresses = EkomApi::inst()->userAddressLayer()->getUserAddresses();
        $billingAddress = null;
        $shippingAddress = null;
        foreach ($addresses as $address) {
            if ((int)$billingAddressId === (int)$address['address_id']) {
                $billingAddress = $address;
            }
            if ((int)$shippingAddressId === (int)$address['address_id']) {
                $shippingAddress = $address;
            }
        }
        if (null === $billingAddress) {
            $billingAddress = $this->getDefaultBillingAddress($userId, $langId);
        }
        if (null === $shippingAddress) {
            $shippingAddress = $this->getDefaultShippingAddress($userId, $langId);
        }
        return [$billingAddress, $shippingAddress, $addresses];
    }


    public function deleteAddress($userId, $addressId)
    {
        $userId = (int)$userId;
        $addressId = (int)$addressId;
        if (false !== ($row = QuickPdo::fetch("select 
is_default_shipping_address, 
is_default_billing_address 
from ek_user_has_address where user_id=$userId and address_id=$addressId"))) {


            EkomApi::inst()->address()->delete(["id" => $addressId]);
            /**
             * If the deleted address was the default shipping or billing address,
             * we transfer this default state to another address (if there is one remaining),
             * so that there is always a default billing and shipping address.
             */
            if (
                '1' === $row['is_default_shipping_address'] ||
                '1' === $row['is_default_billing_address']
            ) {

                if (false !== ($otherAddressId = $this->getFirstUserAddressId($userId))) {
                    $updateData = [];
                    if ('1' === $row['is_default_shipping_address']) {
                        $updateData['is_default_shipping_address'] = 1;
                    }
                    if ('1' === $row['is_default_billing_address']) {
                        $updateData['is_default_billing_address'] = 1;
                    }

                    EkomApi::inst()->userHasAddress()->update($updateData, [
                        'user_id' => $userId,
                        'address_id' => $otherAddressId,
                    ]);
                }

            }
            E::refreshUserContext();
        }
    }

    /**
     * return an addressModel (see top of this document)
     *
     *
     * @return false|array
     */
    public function getUserAddressById($userId, $addressId, $langId = null)
    {
        $addresses = self::getUserAddresses($userId, $langId);
        foreach ($addresses as $address) {
            if ((int)$addressId === (int)$address['address_id']) {
                return $address;
            }
        }
        return false;
    }


    /**
     * Create or update an address and return a boolean: whether or not the address was created (or updated)
     * or not.
     *
     * It's an update if the addressId argument is not null.
     * Otherwise, it's an insert.
     *
     * Note: it is assumed that the fields are validated (by other means) first.
     *
     * The data should contain the following keys:
     *
     * - libelle
     * - phone
     * - phone_prefix
     * - address
     * - city
     * - postcode
     * - supplement
     * - active
     * - country_id
     * - is_default_shipping_address: bool=false,
     * - is_default_billing_address: bool=false,
     *
     *
     *
     */
    public function createAddress($userId, array $data, $addressId = null)
    {
        $maxAddresses = XConfig::get("Ekom.maxUserAddresses");
        $addresses = self::getUserAddresses($userId);
        if (count($addresses) < $maxAddresses) {

            if (null === $addressId) {
                $ret = $this->createNewAddress($userId, $data);
            } else {
                $ret = $this->updateAddress($userId, $addressId, $data);
            }
            if (true === $ret) {
                E::dispatch("user.address-$userId");
            }
            E::refreshUserContext();
            return $ret;
        }
        throw new EkomUserMessageException("Vous ne pouvez pas avoir plus de $maxAddresses adresses");
    }

    /**
     * @param $addressId
     * @param $userId
     * @return bool
     */
    public function setDefaultShippingAddress($addressId, $userId)
    {
        return $this->setDefaultAddress($addressId, $userId, "shipping");
    }


    /**
     * @param $addressId
     * @param $userId
     * @return bool
     */
    public function setDefaultBillingAddress($addressId, $userId)
    {
        return $this->setDefaultAddress($addressId, $userId, "billing");
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public function updateAddress($userId, $addressId, array $data)
    {

        return QuickPdo::transaction(function () use ($userId, $data, $addressId) {
            $userId = (int)$userId;

            EkomApi::inst()->address()->update($data, [
                "id" => $addressId,
            ]);

            if (array_key_exists('is_default_shipping_address', $data) && 1 === (int)$data['is_default_shipping_address']) {
                $this->setDefaultShippingAddress($addressId, $userId);
            }
            if (array_key_exists('is_default_billing_address', $data) && 1 === (int)$data['is_default_billing_address']) {
                $this->setDefaultBillingAddress($addressId, $userId);
            }

        }, function ($e) {
            XLog::error("[Ekom module] - UserLayer.updateNewAddress: $e");
        });
    }


    private function createNewAddress($userId, array $data)
    {
        return QuickPdo::transaction(function () use ($userId, $data) {
            $userId = (int)$userId;
            $data['active'] = 1;

            $addressId = EkomApi::inst()->address()->create($data);

            $userHasAddressData = $data;
            $userHasAddressData["user_id"] = $userId;
            $userHasAddressData["address_id"] = $addressId;
            $isDefaultShipping = 0;
            $isDefaultBilling = 0;


            /**
             * If the user only has one address, then it is automatically
             * the default billing address and the default shipping address.
             */
            $nbAddress = $this->getNbAddresses($userId);
            if (0 === $nbAddress) {
                $isDefaultShipping = 1;
                $isDefaultBilling = 1;
            } else {
                if (array_key_exists("is_default_shipping_address", $data) && 1 === (int)$data['is_default_shipping_address']) {
                    $isDefaultShipping = 1;
                }

                if (array_key_exists("is_default_billing_address", $data) && 1 === (int)$data['is_default_billing_address']) {
                    $isDefaultBilling = 1;
                }
            }


            EkomApi::inst()->userHasAddress()->create($userHasAddressData);

            if (1 === $isDefaultShipping) {
                $this->setDefaultShippingAddress($addressId, $userId);
            }

            if (1 === $isDefaultBilling) {
                $this->setDefaultBillingAddress($addressId, $userId);
            }


        }, function ($e) {
            XLog::error("[Ekom module] - UserLayer.createNewAddress: $e");
        });
    }


    /**
     * @param $addressId
     * @param $userId
     * @return bool
     */
    private function setDefaultAddress($addressId, $userId, $type)
    {
        $userId = (int)$userId;
        $addressId = (int)$addressId;
        $res = QuickPdo::freeQuery("
update ek_user_has_address 
set is_default_" . $type . "_address = IF(address_id = $addressId, 1, 0)
where user_id=$userId
        ");
        return ('00000' !== $res->errorCode());
    }


    private function getNbAddresses($userId)
    {
        $addresses = self::getUserAddresses($userId);
        return count($addresses);
    }


    private function getMinMaxAddressOrder($userId, $type = "shipping")
    {
        $userId = (int)$userId;
        return QuickPdo::fetch("
select min(`order`) as minimum, max(`order`) as maximum 
from ek_user_has_address        
where user_id=$userId
and `type`=:zetype
        
        ", [
            "zetype" => $type,
        ]);
    }


    private function getFirstUserAddressId($userId)
    {
        $userId = (int)$userId;
        return QuickPdo::fetch("select address_id from ek_user_has_address where user_id=$userId order by `order` asc", [], \PDO::FETCH_COLUMN);
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getFormattedNameAndAddress(array $row)
    {
        return [
            $row['libelle'],
            $row['address'] . ", " . $row['postcode'] . " " . $row['city'] . ". " . $row['country'],
        ];
    }


    /**
     * @param $userId
     * @param null $langId
     * @return null|array:addressModel
     * @see UserAddressLayer
     *
     *
     *
     */
    private static function getPreferredAddress($userId, $type)
    {
        $userAddresses = self::getUserAddresses($userId);
        if ($userAddresses) {
            foreach ($userAddresses as $userAddress) {
                if ('1' === $userAddress['is_default_' . $type . '_address']) {
                    return $userAddress;
                }
            }
            return array_shift($userAddresses); // first address if no default address
        }
        return null;

    }
}