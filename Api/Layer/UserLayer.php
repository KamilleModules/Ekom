<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\ArrayTool;
use Bat\HashTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Object\User;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\Checkout\CheckoutPageUtil;
use Module\Ekom\Utils\E;
use Module\ThisApp\ThisAppConfig;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use QuickPdo\QuickPdoStmtTool;


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

    public static function getEarliestAccountCreationDate()
    {
        return QuickPdo::fetch("select min(date(date_creation)) from ek_user where date_creation != '0000-00-00 00:00:00'", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getNbNewUsers($dateStart = null, $dateEnd = null)
    {

        $q = "
select count(*) as count
from ek_user
where 1         
        ";

        $markers = [];
        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date_creation");

        return (int)QuickPdo::fetch($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);

    }

    public static function getUserRepresentationById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("select email from ek_user where id=$id", [], \PDO::FETCH_COLUMN);
    }

    /**
     * @param $hash
     * @return bool,
     *          true if the user becomes active at the end of the dat
     *          false otherwise
     */
    public static function activateByHash($hash)
    {
        $info = QuickPdo::fetch("select * from ek_user where active_hash=:hash", ["hash" => $hash]);
        if (false !== $info) {
            if (0 === (int)$info['active']) {

                QuickPdo::update("ek_user", ['active' => 1], [
                    ['id', "=", $info['id']],
                ]);


                $email = $info['email'];
                $name = $info['first_name'] . " " . $info['last_name'];
                $res = E::sendMail("Ekom/fra/front/customer.new", $email, [
                    "site_name" => ucfirst(ThisAppConfig::SITE_NAME),
                    "uri_site" => E::uriSite(),
                    "email" => $email,
                    "name" => $name,
                ]);

            }
            return true;
        }
        return false;
    }

    public static function updateData($userId, array $data)
    {
        unset($data['id']);
        unset($data['shop_id']);
        unset($data['email']);
        unset($data['pass']);
        unset($data['date_creation']);
        unset($data['active']);
        EkomApi::inst()->user()->update($data, ['id' => (int)$userId]);
    }


    /**
     * @see EkomModels::userInfoModel()
     */
    public static function getUserInfoById($userId, bool $callModules = false)
    {
        $userId = (int)$userId;
        $res = QuickPdo::fetch("
select 
u.*, 
g.name as group_name,
g.label as group_label,
ge.name as gender_name, 
ge.label as gender_label,
ge.long_label as gender_long_label
from ek_user u 
inner join ek_user_group g on g.id=u.user_group_id  
inner join ek_gender ge on ge.id=u.gender_id  
where u.id=$userId");

        if (true === $callModules) {
            Hooks::call('Ekom_UserLayer_decorateUserInfo', $res, $userId);
        }
        return $res;
    }

    public static function getUserInfoByEmail($email)
    {
        return QuickPdo::fetch("select * from ek_user where email=:email", [
            'email' => $email,
        ]);
    }


    /**
     * The current country where the user resides
     */
    public function getCurrentCountry($userId = null, $langId = null)
    {
        $country = null;
        $country = EkomSession::get("userCountry");
        if (null === $country) {
            /**
             * @todo-ling
             * Should we use the billing address?
             * If so, you can embrace the same model as
             * the getCurrentShippingAddress method.
             * But for now, I doubt it, so no.
             *
             */


        }
        return $country;
    }

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
            ConnexionLayer::connectUserById($info['id']);
//            SessionUser::connect(['id' => $info['id']]);
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


    /**
     * Data should be filtered before hand, it's assumed to be sanitized.
     * It's an array containing the following:
     *
     * - ek_user
     *      - ...all fields of table ek_user
     * - groups: array of group names to which the user should belong.
     *              The groups are created if they don't exist.
     *
     * - ...other things, like other tables that modules wish to impact for instance
     *
     *
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
     *              - ?groupIds
     *
     *
     */
    public function createAccount(array $data, &$userError = null)
    {
        $hookData = [];
        $ok = QuickPdo::transaction(function () use ($data, &$hookData) {


            if (!array_key_exists("active_hash", $data['ek_user'])) {
                $data['ek_user']['active_hash'] = HashTool::getRandomHash64();
            }
            $userId = User::getInst()->create($data['ek_user']);


            $hookData = [
                "user_id" => $userId,
                "data" => $data,
            ];
            Hooks::call("Ekom_createAccountAfter", $hookData);


        }, function (\Exception $e) use (&$userError) {
            if (QuickPdoExceptionTool::isDuplicateEntry($e)) {
                $userError = [
                    "duplicateEntry",
                    "Un utilisateur avec cet email existe déjà dans notre base de données",
//                    "An user with this email already exist in our database",
                ];
            } else {
                $userError = [
                    "unknownException",
                    "Une erreur est survenue",
//                    "An error occurred",
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

    public static function getPasswordById(int $userId)
    {
        return QuickPdo::fetch("select pass from ek_user where id=$userId", [], \PDO::FETCH_COLUMN);
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
        });
    }


}