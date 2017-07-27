<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\ArrayTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;


/**
 *
 *
 *
 * addressModel
 * ==================
 * - address_id
 * - first_name
 * - last_name
 * - phone
 * - address
 * - city
 * - postcode
 * - supplement
 * - country
 * //
 * - fName, string: a full name, which format depends on some locale parameters
 * - fAddress, string: a full address, which format depends on some locale parameters
 * - is_default_shipping_address, bool
 * - is_default_billing_address, bool
 *
 *
 *
 *
 */
class UserAddressLayer
{


    /**
     * Return an array of user addresses.
     * Each address is an addressModel (see top of this document)
     *
     *
     */
    public function getUserAddresses($userId, $langId = null)
    {

        EkomApi::inst()->initWebContext();
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

        $userId = (int)$userId;
        $langId = (int)$langId;


        return A::cache()->get("Ekom.UserAddressLayer.getUserAddresses.$langId.$userId", function () use ($userId, $langId) {


            $rows = QuickPdo::fetchAll("
select 
a.id as address_id,        
a.first_name,        
a.last_name,        
a.phone,        
a.address,        
a.city,        
a.postcode,        
a.supplement,        
l.label as country,
l.country_id,
h.is_default_shipping_address,
h.is_default_billing_address


from ek_user_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country_lang l on l.country_id=a.country_id

where 
h.user_id=$userId 
and a.active='1'
and l.lang_id=$langId


order by h.`order` asc 
        
        
        ");

            // depending on the shop's country?
            foreach ($rows as $k => $row) {


                list($fName, $fAddress) = $this->getFormattedNameAndAddress($row);
                $rows[$k]['fName'] = $fName;
                $rows[$k]['fAddress'] = $fAddress;
            }

            return $rows;
        }, [
            "ek_address",
            "ek_user_has_address",
            "ek_country_lang.delete",
            "ek_country_lang.update",
        ]);
    }


    public function deleteAddress($userId, $addressId)
    {
        $userId = (int)$userId;
        $addressId = (int)$addressId;
        if (false !== ($row = QuickPdo::fetch("select * from ek_user_has_address where user_id=$userId and address_id=$addressId"))) {
            EkomApi::inst()->address()->delete(["id" => $addressId]);
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
        $addresses = $this->getUserAddresses($userId, $langId);
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
     * - first_name
     * - last_name
     * - phone
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
        if (null === $addressId) {
            return $this->createNewAddress($userId, $data);
        } else {
            return $this->updateAddress($userId, $addressId, $data);
        }
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

    /**
     * @return false|array
     */
    public function getDefaultShippingAddress($userId, $langId = null)
    {
        return $this->getDefaultAddress($userId, 'shipping', $langId);
    }

    /**
     * @return false|array
     */
    public function getDefaultBillingAddress($userId, $langId = null)
    {
        return $this->getDefaultAddress($userId, 'billing', $langId);
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


    private function getFormattedNameAndAddress(array $row)
    {
        return [
            $row['first_name'] . " " . $row['last_name'],
            $row['address'] . ", " . $row['postcode'] . " " . $row['city'] . ". " . $row['country'],
        ];
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


    /**
     * @param $userId
     * @param null $langId
     * @return false|array representing the default shipping address model
     *
     *
     */
    private function getDefaultAddress($userId, $type, $langId = null)
    {
        $userAddresses = $this->getUserAddresses($userId, $langId);
        foreach ($userAddresses as $userAddress) {
            if ('1' === $userAddress['is_default_' . $type . '_address']) {
                return $userAddress;
            }
        }
        return false;

    }


    private function getNbAddresses($userId)
    {
        $addresses = $this->getUserAddresses($userId);
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
}