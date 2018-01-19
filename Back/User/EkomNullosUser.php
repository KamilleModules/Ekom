<?php



namespace Module\Ekom\Back\User;


use Module\NullosAdmin\Authenticate\User\NullosUser;

class EkomNullosUser extends NullosUser
{


    /**
     * Use this method to retrieve the context values in ekom back.
     *
     * More info in:
     * class-modules/Ekom/doc/backoffice/backoffice-brainstorm.md
     *
     *
     * @param $key
     * @param int $default
     * @return int
     */
    public static function getEkomValue($key, $default = 0)
    {
        $ekom = self::get('ekom', []);
        if (array_key_exists($key, $ekom)) {
            return $ekom[$key];
        }
        return $default;
    }
}