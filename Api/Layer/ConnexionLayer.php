<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use BeeFramework\Component\Http\Session\Session;
use Core\Services\Hooks;
use Http4All\Header\Http4AllHeader;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Exception\UserNotConnectedException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ConnexionLayer
{

    /**
     * Return either one or all the user connexion data.
     * By default, returns all user connexion data.
     * The two arguments of this function only apply if you want to retrieve ONE particular entry of
     * the user connexion data.
     *
     *
     * @param null $key
     * @param null $default
     * @return bool|mixed
     */
    public static function getUserConnexionData($key = null, $default = null)
    {
        if (null === $key) {
            if (SessionUser::isConnected()) {
                return SessionUser::getAll();
            }
            return false;
        }
        return SessionUser::getValue($key, $default);
    }

    public static function connectUserById(int $userId)
    {
        $data = ConnexionLayer::buildConnexionDataByUserId($userId);
        EkomApi::inst()->connexionLayer()->connect($data);
        Hooks::call("Ekom_onUserConnectedAfter");
    }


    /**
     * @return array
     * @see EkomModels::connexionData()
     *
     * The connexionData array is a VERY IMPORTANT array, since it helps creating the UserContext.
     * @see E::getUserContext()
     *
     * In fact, the connexionData contains all the properties of the UserContext, and more.
     *
     * The connexionData array is created at a strategic moment in Ekom's lifecycle: only once
     * when the user connects.
     * This is the perfect moment to fetch a lot of data for the user, since they will be "cached"
     * in the session for every subsequent calls.
     *
     * That's exactly what happens: once the connexionData are created, the UserContext (which
     * is created once on EVERY page) can simply build itself from those connexionData
     * without having to query from the database (since all data are already present in the connexionData).
     *
     *
     * connexionData update: Watch out for changes!
     * -----------------------
     * However, with this strategy, we need to be careful when the user updates her data (using the my_account gui).
     * Since the connexionData includes information such as the default shipping address id of the user,
     * if the user changes her default shipping address, this change needs to be reflected in the connexionData
     * as well (since the UserContext always build itself using those connexionData).
     * So at some strategic points (for instance when the "add a shipping address form" is processed),
     * Ekom or its modules need to trigger the changes.
     *
     * To make it easier, Ekom provides the E::refreshUserContext method, which in turn will update the
     * connexionData if appropriate (if the user is logged in) for you.
     *
     *
     *
     */
    public static function buildConnexionDataByUserId(int $userId)
    {

        $userConnexionData = self::getAccountInfoByUserId($userId);
        self::decorateUserConnexionDataWithShippingAddress($userId, $userConnexionData);
        self::decorateUserConnexionDataWithBillingAddress($userId, $userConnexionData);

        Hooks::call("Ekom_Connexion_decorateUserConnexionData", $userConnexionData, $userId);
        return $userConnexionData;
    }


    /**
     * Call this method only if you know you are connected.
     * @see E::buildUserContext()
     *
     * @return mixed
     */
    public static function getConnexionData()
    {
        return SessionUser::getAll();
    }


    //--------------------------------------------
    // FRONT OFFICE USER
    //--------------------------------------------
    /**
     * @param array $data
     *      - id: the user id
     */
    public function connect(array $data)
    {
        SessionUser::connect($data, E::conf("sessionTimeout"));
        /**
         * @proposal: We start the checkout data over every time the user connects.
         */
        CurrentCheckoutData::clean();
    }

    public function isConnected()
    {
        return SessionUser::isConnected();
    }

    public function disconnect()
    {
        $destroyCookie = false;
        SessionUser::disconnect($destroyCookie);
    }

    public function getUserId($default = false)
    {
        if (SessionUser::isConnected()) {
            return SessionUser::getValue("id");
        }
        if (false === $default) {
            throw new UserNotConnectedException("The user is not connected");
        }
        return $default;
    }

    public function getUserData($key, $default = null)
    {
        if (SessionUser::isConnected()) {
            return SessionUser::getValue($key, $default);
        }
        return $default;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getAccountInfoByUserId(int $userId)
    {
        return QuickPdo::fetch("
select 
u.id,
u.email,
u.user_group_id,
ug.name as user_group_name,
ug.label as user_group_label,

u.gender_id,
g.name as gender_name,
g.label as gender_label,
g.long_label as gender_long_label


from ek_user u 
inner join ek_gender g on g.id=u.gender_id
inner join ek_user_group ug on ug.id=u.user_group_id


where u.id=$userId

");
    }


    private static function decorateUserConnexionDataWithShippingAddress(int $userId, array &$userConnexionData)
    {
        $row = QuickPdo::fetch("
select 
h.address_id as default_shipping_address_id,
a.country_id as default_shipping_country_id,
c.iso_code as default_shipping_country

from ek_user_has_address h 
left join ek_address a on a.id=h.address_id
left join ek_country c on c.id=a.country_id


where 
h.user_id=$userId
and h.is_default_shipping_address=1

");

        if (false === $row) {
            $userConnexionData['default_shipping_address_id'] = null;
            $userConnexionData['default_shipping_country_id'] = null;
            $userConnexionData['default_shipping_country'] = null;
        } else {
            $userConnexionData['default_shipping_address_id'] = $row['default_shipping_address_id'];
            $userConnexionData['default_shipping_country_id'] = $row['default_shipping_country_id'];
            $userConnexionData['default_shipping_country'] = $row['default_shipping_country'];
        }
    }

    private static function decorateUserConnexionDataWithBillingAddress(int $userId, array &$userConnexionData)
    {


        $row = QuickPdo::fetch("
select 
h.address_id as default_billing_address_id,
a.country_id as default_billing_country_id,
c.iso_code as default_billing_country

from ek_user_has_address h 
left join ek_address a on a.id=h.address_id
left join ek_country c on c.id=a.country_id


where 
h.user_id=$userId
and h.is_default_billing_address=1

");

        if (false === $row) {
            $userConnexionData['default_billing_address_id'] = null;
            $userConnexionData['default_billing_country_id'] = null;
            $userConnexionData['default_billing_country'] = null;
        } else {
            $userConnexionData['default_billing_address_id'] = $row['default_billing_address_id'];
            $userConnexionData['default_billing_country_id'] = $row['default_billing_country_id'];
            $userConnexionData['default_billing_country'] = $row['default_billing_country'];
        }
    }
}