<?php


namespace Module\Ekom\Helper\Stats\Modules;



class PageNotFoundControllerModule extends DefaultListControllerModule
{
    public static function getModuleHandler()
    {
        return self::getModuleHandlerByViewId("back/stats/page_not_found");
    }
}


