<?php


namespace Module\Ekom\Utils\EkomRootUser;


use Module\Ekom\Session\EkomSession;

class EkomRootUser
{
    private static $prepared = false;

    public static function connectAs($userId)
    {
        self::prepare();
        $rootUser = EkomSession::get('ekomRootUser');
        $rootUser['current_user_id'] = $userId;
        self::write($rootUser);
    }

    public static function isConnectedAs($userId)
    {
        self::prepare();
        $rootUser = EkomSession::get('ekomRootUser');
        return ((int)$rootUser['current_user_id'] === (int)$userId);

    }

    public static function getCurrentUserId()
    {
        self::prepare();
        $rootUser = EkomSession::get('ekomRootUser');
        if (0 === $rootUser['current_user_id']) {
            return null;
        }
        return (int)$rootUser['current_user_id'];

    }


    public static function destroyCurrentUser()
    {
        self::prepare();
        $rootUser = EkomSession::get('ekomRootUser');
        $rootUser['current_user_id'] = 0;
        self::write($rootUser);
    }


    private static function prepare()
    {
        if (false === self::$prepared) {
            self::$prepared = true;
            $rootUser = EkomSession::get('ekomRootUser', [
                /**
                 * Root user can connect as anybody
                 */
                "current_user_id" => 0,
            ]);
            EkomSession::set('ekomRootUser', $rootUser);
        }
    }


    private static function write(array $rootUser)
    {
        EkomSession::set('ekomRootUser', $rootUser);
    }

}