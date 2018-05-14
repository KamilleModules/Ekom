<?php


namespace Module\Ekom\Helper\Stats\Modules;


class BestCategoriesControllerModule extends DefaultListControllerModule
{

    public static function getModuleHandler()
    {
        return self::getModuleHandlerByViewId("back/stats/best_categories");
    }
}