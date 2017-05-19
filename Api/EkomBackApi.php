<?php


namespace Module\Ekom\Api;


class EkomBackApi
{

    private static $inst;


    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new static();
        }
        return self::$inst;
    }



    public function insertProduct(array $product){




    }



}