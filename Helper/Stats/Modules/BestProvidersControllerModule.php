<?php


namespace Module\Ekom\Helper\Stats\Modules;


class BestProvidersControllerModule extends DefaultListControllerModule
{

    public static function getModuleHandler()
    {
        return self::getModuleHandlerByViewId("back/stats/best_providers");
    }
}


