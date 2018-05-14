<?php


namespace Module\Ekom\Helper\Stats\Modules;


class BestSellersControllerModule extends DefaultListControllerModule
{

    public static function getModuleHandler()
    {
        return self::getModuleHandlerByViewId("back/stats/best_sellers");
    }
}


