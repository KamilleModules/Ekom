<?php


namespace Module\Ekom\Back\Util\QuickStartWizard;


use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;

class QuickStartWizard
{


    public static function checkApp(&$message = null)
    {
        $isOk = true;
        if (
            false === self::isCurrencySet() ||
            false === self::isLangSet() ||
            false === self::isShopSet()
        ) {
            if (false === self::hasCurrency()) {
                $message = "There is no entry in currency. Please add one.";
                $isOk = false;
            } elseif (false === self::hasLang()) {
                $message = "There is no entry in lang. Please add one.";
                $isOk = false;
            } elseif (false === self::hasShop()) {
                $message = "There is no entry in shop. Please add one.";
                $isOk = false;
            } elseif (false === self::hasShopHasCurrency()) {
                $message = "There is no entry in shop_has_currency. Please add one.";
                $isOk = false;
            } elseif (false === self::hasShopHasLang()) {
                $message = "There is no entry in shop_has_lang. Please add one.";
                $isOk = false;
            }

        }
        return $isOk;
    }

    public static function isCurrencySet()
    {
        $itemId = EkomNullosUser::getEkomValue("currency_id");
        if (0 === (int)$itemId) {
            return false;
        }
        return true;
    }

    public static function isLangSet()
    {
        $itemId = EkomNullosUser::getEkomValue("lang_id");
        if (0 === (int)$itemId) {
            return false;
        }
        return true;
    }

    public static function isShopSet()
    {
        $itemId = EkomNullosUser::getEkomValue("shop_id");
        if (0 === (int)$itemId) {
            return false;
        }
        return true;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected static function hasCurrency()
    {
        $count = QuickPdo::fetch("
select count(*) as count from ek_currency        
        ", [], \PDO::FETCH_COLUMN);
        return ($count > 0);
    }

    protected static function hasLang()
    {
        $count = QuickPdo::fetch("
select count(*) as count from ek_lang        
        ", [], \PDO::FETCH_COLUMN);
        return ($count > 0);
    }

    protected static function hasShop()
    {
        $count = QuickPdo::fetch("
select count(*) as count from ek_shop        
        ", [], \PDO::FETCH_COLUMN);
        return ($count > 0);
    }

    protected static function hasShopHasCurrency()
    {
        $defaultShopId = self::getDefaultShopId();
        $count = QuickPdo::fetch("
select count(*) as count from ek_shop_has_currency where shop_id=$defaultShopId      
        ", [], \PDO::FETCH_COLUMN);
        return ($count > 0);
    }

    protected static function hasShopHasLang()
    {
        $defaultShopId = self::getDefaultShopId();
        $count = QuickPdo::fetch("
select count(*) as count from ek_shop_has_lang where shop_id=$defaultShopId      
        ", [], \PDO::FETCH_COLUMN);
        return ($count > 0);
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getDefaultShopId()
    {
        $itemId = EkomNullosUser::getEkomValue("shop_id");
        if (0 !== (int)$itemId) {
            return (int)$itemId;
        }
        // not in the session yet, but it should exist in the database
        $row = QuickPdo::fetch("select id from ek_shop order by id asc");
        return (int)$row['id'];
    }
}
