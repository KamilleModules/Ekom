<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Core\Services\Hooks;
use Http4All\Header\Http4AllHeader;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Exception\UserNotConnectedException;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;

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


    public static function getConnexionDataByUserId($userId, $langId=null)
    {
        $langId = E::getLangId($langId);

        $userGroupNames = EkomApi::inst()->userLayer()->getUserGroupNames($userId);
        $shippingAddress = UserAddressLayer::getPreferredShippingAddress($userId, $langId);
        $userShippingCountry = false;
        if (null !== $shippingAddress) {
            $userShippingCountry = $shippingAddress['country_iso_code'];
        }


        $userConnexionData = [
            'id' => $userId,
            'userBrowserCountry' => Http4AllHeader::getUserPreferredCountry("FR"),
            'userShippingCountry' => $userShippingCountry, // false|string
            /**
             * array of groupId => groupName
             */
            'userGroups' => $userGroupNames,
        ];

        Hooks::call("Ekom_Connexion_decorateUserConnexionData", $userConnexionData);
        return $userConnexionData;
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


}