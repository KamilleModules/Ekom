<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Utils\E;

class ConnexionLayer
{


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
    }

    public function isConnected()
    {
        return SessionUser::isConnected();
    }

    public function getUserId($default = false)
    {
        if (SessionUser::isConnected()) {
            return SessionUser::getValue("id");
        }
        if (false === $default) {
            throw new EkomApiException("The user is not connected");
        }
        return $default;
    }
}