<?php


namespace Module\Ekom\Session;


use Bat\SessionTool;

/**
 * The intent is that EVERY session variable created by the ekom module is created
 * via this class, which basically creates ONE ekom namespace (so that ekom is a good neighbour
 * to other modules)
 */
class EkomSession
{


    /**
     * I recommend that you use dot notation for $k.
     * For instance k=country.city
     */
    public static function set($k, $v)
    {
        SessionTool::start();
        if (false === array_key_exists('ekom', $_SESSION)) {
            $_SESSION['ekom'] = [];
        }
        $_SESSION['ekom'][$k] = $v;
    }

    public static function get($k, $default = null)
    {
        SessionTool::start();
        if (array_key_exists('ekom', $_SESSION)) {
            if (array_key_exists($k, $_SESSION['ekom'])) {
                return $_SESSION['ekom'][$k];
            }
        }
        return $default;
    }
}