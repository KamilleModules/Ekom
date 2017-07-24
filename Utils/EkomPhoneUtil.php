<?php


namespace Module\Ekom\Utils;


class EkomPhoneUtil
{


    public static function createInternationalPhone($localNumber, $prefix)
    {
        return $prefix . ":" . $localNumber;
    }

    public static function getPhoneInfo($internationalNumber, $defaultPrefix = "")
    {
        $p = explode(':', $internationalNumber);
        $prefix = $defaultPrefix;
        $phone = "";
        if (2 === count($p)) {
            $prefix = $p[0];
            $phone = $p[1];
        } else {
            $phone = $p[0];
        }
        return [$prefix, $phone];
    }

}