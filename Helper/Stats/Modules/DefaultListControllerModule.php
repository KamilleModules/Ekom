<?php


namespace Module\Ekom\Helper\Stats\Modules;


use Core\Services\A;

class DefaultListControllerModule
{

    protected static function getModuleHandlerByViewId(string $viewId)
    {
        return function ($dateStart, $dateEnd) use ($viewId) {

            $template = "Ekom/All/Stats/OrdersAndGeneralStats/default_list";
            $conf = [];

            //--------------------------------------------
            // BEST CATS
            //--------------------------------------------
            $moduleName = "Ekom";
            $context = [
                "date_start" => $dateStart,
                "date_end" => $dateEnd,
            ];
            $listConfig = A::getMorphicListConfig($moduleName, $viewId, $context);
            $conf['listConfig'] = $listConfig;
            return [
                $template,
                $conf,
            ];

        };
    }


}


