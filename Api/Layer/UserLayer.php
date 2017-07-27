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
 * - is_preferred_shipping_address, bool
 * - is_preferred_billing_address, bool
 *
 *
 *
 *
 */
class UserLayer
{


    //--------------------------------------------
    // USER CONNEXION
    //--------------------------------------------
    public function userExistsByEmail($email)
    {
        $res = QuickPdo::fetch("select id from ek_user where email=:email", [
            "email" => $email,
        ]);
        if (false === $res) {
            return false;
        }
        return true;
    }


    /**
     * @param array $info
     *      - id: the user id
     * @throws EkomException
     * @return void
     */
    public function connectUser(array $info)
    {
        if (array_key_exists('id', $info)) {
            SessionUser::connect(['id' => $info['id']]);
        } else {
            throw new EkomException("Id not found");
        }
    }

    public function getUserId()
    {
        if (SessionUser::isConnected()) {
            return SessionUser::getValue("id");
        }
        throw new EkomApiException("The user is not connected");
    }


    //--------------------------------------------
    // USER PERSONAL
    //--------------------------------------------
    /**
     * Used to get the user account (view/template) information
     */
    public function getAccountInfo($userId)
    {
        $groupId2Names = $this->getUserGroupNames($userId);
        $info = $this->getUserInfo($userId);
        $info['groups'] = $groupId2Names;
        return $info;
    }





    /**
     * Data should be filtered before hand, it's assumed to be sanitized.
     *
     * - userError: an array with the following entries:
     *      - 0: code, an arbitrary code representing the error
     *      - 1: msg, an error message written in english
     *
     *
     * @return false|array
     *              If it's an array, it's the hookData array,
     *              containing the following:
     *              - userId
     *              - data
     *              - ?groupId
     *
     *
     */
    public function createAccount(array $data, &$userError = null)
    {
        $hookData = [];
        $ok = QuickPdo::transaction(function () use ($data, &$hookData) {

            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");


            /**
             * Todo: let modules control this,
             * for instance, the newsletter might be 1 in some cases,
             * or active might be 0, ...
             */
            $data['shop_id'] = $shopId;
            $data['active'] = 1;
            $data['date_creation'] = date('Y-m-d H:i:s');
            $data['newsletter'] = 0;


            $userId = EkomApi::inst()->user()->create($data);


            $hookData = [
                "user_id" => $userId,
                "data" => $data,
            ];


            if (array_key_exists("group", $data)) {
                $group = $data["group"];
                $groupId = EkomApi::inst()->userGroupLayer()->createGroupIfNotExist([
                    "name" => $group,
                    "shop_id" => $shopId,
                ]);
                EkomApi::inst()->userHasGroupLayer()->bindUser2Group($userId, $groupId);
                $hookData['groupId'] = $groupId;
            }
            Hooks::call("Ekom_createAccountAfter", $hookData);


        }, function (\Exception $e) use (&$userError) {
            if (QuickPdoExceptionTool::isDuplicateEntry($e)) {
                $userError = [
                    "duplicateEntry",
                    "An user with this email already exist in our database",
                ];
            } else {
                $userError = [
                    "unknownException",
                    "An error occurred",
                ];
            }
            XLog::error("$e");

        });

        if (false === $ok) {
            return false;
        }
        return $hookData;
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
            if (false !== ($addr = EkomApi::inst()->userAddressLayer()->getDefaultShippingAddress($userId, $langId))) {
                return $addr['country_id'];
            }
        }
        return 0;
    }



    public function getUserGroupIds($userId)
    {
        return A::cache()->get("Ekom.UserLayer.getUserGroupIds.$userId", function () use ($userId) {

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

    public function getUserGroupNames($userId)
    {
        return A::cache()->get("Ekom.UserLayer.getUserGroupNames.$userId", function () use ($userId) {

            $userId = (int)$userId;
            return QuickPdo::fetchAll("
select g.id, g.name 
from ek_user_group g 
inner join ek_user_has_user_group h on h.user_group_id=g.id
where h.user_id=$userId             
            ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }, [
            "ek_user_has_user_group.create",
            "ek_user_has_user_group.delete.$userId",
            "ek_user_has_user_group.update.$userId",
            "ek_user_group.update.$userId",
        ]);
    }

    public function getUserInfo($userId)
    {
        return A::cache()->get("Ekom.UserLayer.getUserInfo.$userId", function () use ($userId) {

            $userId = (int)$userId;
            return QuickPdo::fetch("
select *
from ek_user
where id=$userId             
            ");
        }, [
            "ek_user.update.$userId",
            "ek_user.delete.$userId",
        ]);
    }






}