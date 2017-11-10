<?php


namespace Module\Ekom\Utils\Service;


use Module\Ekom\Exception\EkomInvalidArgumentException;

class EcpServiceUtil
{

    public static function get($key, $throwEx = true, $default = null)
    {
        $pool = $_POST;


        if (array_key_exists($key, $pool)) {
            $ret = $pool[$key];
            if ('true' === $ret) {
                $ret = true;
            }
            if ('false' === $ret) {
                $ret = false;
            }
            return $ret;
        }
        if (true === $throwEx) {
            throw EkomInvalidArgumentException::create()->setMissingKey($key);
        }
        return $default;
    }

}