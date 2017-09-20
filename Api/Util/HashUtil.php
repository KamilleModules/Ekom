<?php


namespace Module\Ekom\Api\Util;



class HashUtil
{

    public static function createHashByArray(array $arr){
        ksort($arr);
        return hash('ripemd160', serialize($arr));
    }
}