<?php


namespace Module\Ekom\Utils;


class EkomPhoneUtil
{


    public static function createInternationalPhone($localNumber, $prefix)
    {
        return $prefix . ":" . $localNumber;
    }

    public static function getPhoneInfo($internationalNumber)
    {
        $p = explode(':', $internationalNumber);
        return $p[0] . ":" . $p[1];
    }

}