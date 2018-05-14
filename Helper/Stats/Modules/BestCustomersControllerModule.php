<?php


namespace Module\Ekom\Helper\Stats\Modules;


class BestCustomersControllerModule extends DefaultListControllerModule
{

    public static function getModuleHandler()
    {
        return self::getModuleHandlerByViewId("back/stats/best_customers");
    }
}


