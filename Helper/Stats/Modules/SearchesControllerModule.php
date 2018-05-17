<?php


namespace Module\Ekom\Helper\Stats\Modules;



class SearchesControllerModule extends DefaultListControllerModule
{
    public static function getModuleHandler()
    {
        return self::getModuleHandlerByViewId("back/stats/searches");
    }
}


