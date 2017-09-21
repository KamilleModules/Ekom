<?php


namespace Module\Ekom\Api\Util;


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
}