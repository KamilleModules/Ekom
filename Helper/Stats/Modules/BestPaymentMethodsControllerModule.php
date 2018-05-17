<?php


namespace Module\Ekom\Helper\Stats\Modules;


class BestPaymentMethodsControllerModule extends DefaultListControllerModule
{

    public static function getModuleHandler()
    {
        return self::getModuleHandlerByViewId("back/stats/best_payment_methods");
    }
}


