<?php


namespace Module\Ekom\Api\Util;


use Bat\HashTool;
use Module\Ekom\Utils\E;

class HashUtil
{

    public static function createHashByArray(array $arr)
    {
        if ($arr) {
            ksort($arr);
            return hash('ripemd160', serialize($arr));
        }
        return '';
    }

    public static function getBoxHash(array $customParams = [], array $boxOptions = [])
    {

        $all = array_merge([
            "is_backoffice" => (int)E::isBackOffice(),
        ], E::getUserContext(), $boxOptions, $customParams);
        asort($all);
        return HashTool::getHashByArray($all);


    }
}