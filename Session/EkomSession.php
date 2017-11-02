<?php


namespace Module\Ekom\Session;


use Bat\SessionTool;

/**
 * This is a helper class, the goal being to put all ekom related session variables
 * under the namespace ekom.
 * Note: you can use this helper class if it helps you, or anything else.
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


    public static function has($k)
    {
        SessionTool::start();
        if (array_key_exists('ekom', $_SESSION)) {
            return array_key_exists($k, $_SESSION['ekom']);
        }
        return false;
    }

    public static function remove($k)
    {
        SessionTool::start();
        if (array_key_exists('ekom', $_SESSION)) {
            unset($_SESSION['ekom'][$k]);
        }
        return false;
    }


    public static function pick($k, $default = null)
    {
        SessionTool::start();
        if (array_key_exists('ekom', $_SESSION)) {
            if (array_key_exists($k, $_SESSION['ekom'])) {
                $value = $_SESSION['ekom'];
                unset($_SESSION['ekom'][$k]);
                return $value;
            }
        }
        return $default;
    }
}