<?php


namespace Module\Ekom\Api\Link\User;


use Module\Ekom\Utils\Api\EkomApiLink;
use Module\Ekom\Utils\E;
use SaveOrmObject\Object\Ek\UserObject;

class UserLink  extends EkomApiLink
{


    /**
     * userModel:
     * - ...all ek_user fields:
     * - ?groups: array of group item, each of which containing the ek_user_group fields
     * - ?addresses: array of addresses item, each of which containing the ek_user_group fields
     * - ?hooks: array of items processed by other modules
     */
    public static function createUser(array $data)
    {


        /**
         * @todo-ling:
         *
         * To create an user, fill:
         *
         * - ek_user
         * - ek_user_has_user_group
         *
         *
         *
         */
        if(false){

        $groups = (array_key_exists("groups", $data)) ? $data['groups'] : [];
        $addresses = (array_key_exists("addresses", $data)) ? $data['addresses'] : [];
        $hooks = (array_key_exists("hooks", $data)) ? $data['hooks'] : [];

        unset($data['groups']);
        unset($data['addresses']);
        unset($data['hooks']);


//        self::handleMissing(['email'])


        $shopId = E::getShopId($shopId);
        UserObject::createByEmailShopId($email, $shopId);
        }


    }

}