<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\ArrayTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use QuickPdo\QuickPdo;


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
 * - is_preferred, bool: whether or not this is the favorite user address
 *
 *
 *
 *
 */
class UserLayer
{


    public function getUserId()
    {
        if (SessionUser::isConnected()) {
            return SessionUser::getValue("id");
        }
        throw new EkomApiException("The user is not connected");
    }

    public function deleteAddress($userId, $addressId)
    {
        $userId = (int)$userId;
        $addressId = (int)$addressId;
        if (false !== QuickPdo::fetch("select user_id from ek_user_has_address where user_id=$userId and address_id=$addressId")) {
            EkomApi::inst()->address()->delete(["id" => $addressId]);
        }
    }

    /**
     * return an addressModel (see top of this document)
     *
     *
     * @return false|array
     */
    public function getUserShippingAddressById($userId, $addressId, $langId = null)
    {

        $userId = (int)$userId;
        $addressId = (int)$addressId;
        EkomApi::inst()->initWebContext();
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

        return A::cache()->get("Module.Ekom.Api.Layer.UserLayer.getUserShippingAddressById.$langId.$userId.$addressId", function () use ($userId, $langId, $addressId) {

            if (false !== ($row = QuickPdo::fetch("

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
h.`order`


from ek_user_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country_lang l on l.country_id=a.country_id

where 
a.id=$addressId
and h.user_id=$userId 
and h.type='shipping'
and a.active='1'
and l.lang_id=$langId
                 
        "))
            ) {
                $minMax = $this->getMinMaxAddressOrder($userId);
                $isPreferred = ((int)$row['order'] === (int)$minMax['minimum']);

                list($fName, $fAddress) = $this->getFormattedNameAndAddress($row);

                $row['fName'] = $fName;
                $row['fAddress'] = $fAddress;
                $row['is_preferred'] = $isPreferred;

                return $row;
            }
            return false;
        }, [
            "ek_user_has_address.delete.$userId",
            "ek_user_has_address.update.$userId",
            "ek_address.delete.$addressId",
            "ek_address.update.$addressId",
            "ek_country_lang.delete",
            "ek_country_lang.update",
        ]);
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
     * - type: the type of address: billing|shipping
     * - is_preferred: bool=false, in case the address is of type shipping, whether
     *                  or not this address should be the preferred one.
     *
     *
     *
     */
    public function createAddress($userId, array $data, $addressId = null)
    {
        if (null === $addressId) {
            return $this->createNewAddress($userId, $data);
        } else {
            return $this->updateNewAddress($userId, $data, $addressId);
        }
    }

    private function updateNewAddress($userId, array $data, $addressId)
    {

        return QuickPdo::transaction(function () use ($userId, $data, $addressId) {
            $userId = (int)$userId;

            $addressData = ArrayTool::superimpose($data, [
                "first_name" => "",
                "last_name" => "",
                "phone" => "",
                "address" => "",
                "city" => "",
                "postcode" => "",
                "supplement" => "",
                "active" => "1",
                "country_id" => 0,
            ]);


            EkomApi::inst()->address()->update($addressData, [
                "id" => $addressId,
            ]);


            if (array_key_exists("is_preferred", $data)) {
                $isPreferred = (bool)$data['is_preferred'];
                if (true === $isPreferred) {
                    $minMax = $this->getMinMaxAddressOrder($userId);
                    $min = $minMax['minimum'];
                    $order = $min - 1;
                    $userHasAddressData = [
                        "order" => $order,
                    ];
                    EkomApi::inst()->userHasAddress()->update($userHasAddressData, [
                        "user_id" => $userId,
                        "address_id" => $addressId,
                        "type" => "shipping",
                    ]);
                }
            }


        }, function ($e) {
            XLog::error("[Ekom module] - UserLayer.createAddress: $e");
        });
    }


    private function createNewAddress($userId, array $data)
    {

        return QuickPdo::transaction(function () use ($userId, $data) {
            $userId = (int)$userId;

            $addressData = ArrayTool::superimpose($data, [
                "first_name" => "",
                "last_name" => "",
                "phone" => "",
                "address" => "",
                "city" => "",
                "postcode" => "",
                "supplement" => "",
                "active" => "1",
                "country_id" => 0,
            ]);


            $address = EkomApi::inst()->address()->create($addressData);

            $minMax = $this->getMinMaxAddressOrder($userId);
            $min = $minMax['minimum'];
            $max = $minMax['maximum'];


            $isPreferred = false;
            if (array_key_exists("is_preferred", $data)) {
                $isPreferred = (bool)$data['is_preferred'];
            }

            $order = (true === $isPreferred) ? $min - 1 : $max + 1;

            $userHasAddressData = ArrayTool::superimpose($data, [
                "type" => "shipping",
                "order" => $order,
            ]);
            $userHasAddressData["user_id"] = $userId;
            $userHasAddressData["address_id"] = $address;

            EkomApi::inst()->userHasAddress()->create($userHasAddressData);

        }, function ($e) {
            XLog::error("[Ekom module] - UserLayer.createAddress: $e");
        });
    }


    /**
     * This method returns the user favorite country,
     * so that when display the list of countries,
     * we can pre-select her favorite.
     *
     * The heuristics we use is simply use the country of her oreferred shipping address.
     *
     */
    public function getUserPreferredCountry($userId = null, $langId = null)
    {
        if (null === $userId) {
            if (true === SessionUser::isConnected()) {
                $userId = SessionUser::getValue("id");
            }
        }
        if (null !== $userId) {
            if (false !== ($addr = $this->getPreferredShippingAddress($userId, $langId))) {
                return $addr['country_id'];
            }
        }
        return 0;
    }


    /**
     * Return an array of user shipping addresses.
     * Each address is an addressModel (see top of this document)
     *
     *
     */
    public function getUserShippingAddresses($userId, $langId = null)
    {

        EkomApi::inst()->initWebContext();
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

        $userId = (int)$userId;
        $langId = (int)$langId;


        return A::cache()->get("Module.Ekom.Api.Layer.UserLayer.getUserShippingAddresses.$langId.$userId", function () use ($userId, $langId) {


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
l.label as country


from ek_user_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country_lang l on l.country_id=a.country_id

where 
h.user_id=$userId 
and h.type='shipping'
and a.active='1'
and l.lang_id=$langId


order by h.`order` asc 
        
        
        ");

            // depending on the shop's country?
            foreach ($rows as $k => $row) {


                list($fName, $fAddress) = $this->getFormattedNameAndAddress($row);
                $rows[$k]['fName'] = $fName;
                $rows[$k]['fAddress'] = $fAddress;
                $rows[$k]['is_preferred'] = (0 === $k);
            }

            return $rows;
        }, [
            "ek_user_has_address.$userId",
            "ek_country_lang.delete",
            "ek_country_lang.update",
        ]);
    }


    public function getUserGroupIds($userId)
    {
        return A::cache()->get("Module.Ekom.Api.Layer.UserLayer.getUserGroupIds.$userId", function () use ($userId) {

            $userId = (int)$userId;
            return EkomApi::inst()->userHasUserGroup()->readValues("user_group_id", [
                "where" => [
                    ["user_id", "=", $userId],
                ],
            ]);
        }, [
            "ek_user_has_user_group.delete.$userId",
            "ek_user_has_user_group.update.$userId",
        ]);
    }


    /**
     * @param $userId
     * @param null $langId
     * @return false|array representing the preferred shipping address:
     *
     *      - city
     *      - postcode
     *      - address
     *      - country_id
     *      - country_label
     *
     */
    public function getPreferredShippingAddress($userId, $langId = null)
    {

        //  todo : cache
        EkomApi::inst()->initWebContext();

        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        $userId = (int)$userId;
        $langId = (int)$langId;


        return QuickPdo::fetch("
select 
a.city,
a.postcode,
a.address,
a.country_id,
l.label as country_label

from ek_user_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country_lang l on l.country_id=a.country_id 

where h.user_id=$userId
and h.type='shipping'
and a.active=1
and l.lang_id=$langId

order by h.`order` asc


            
            ");
    }




    //--------------------------------------------
    //
    //--------------------------------------------
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


    private function getFormattedNameAndAddress(array $row)
    {
        return [
            $row['first_name'] . " " . $row['last_name'],
            $row['address'] . ", " . $row['postcode'] . " " . $row['city'] . ". " . $row['country'],
        ];
    }
}