<?php


namespace Module\Ekom\Helper\Stats\Modules;



class BestSellsControllerModule extends DefaultListControllerModule
{
    public static function getModuleHandler()
    {
        return self::getModuleHandlerByViewId("back/stats/best_sells");
    }
}


