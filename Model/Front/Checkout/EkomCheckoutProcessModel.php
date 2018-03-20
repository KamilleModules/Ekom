<?php


namespace Module\Ekom\Model\Front\Checkout;


use Kamille\Services\XLog;
use Module\Ekom\Utils\CheckoutProcess\EkomCheckoutProcess;
use Module\Ekom\Utils\E;

class EkomCheckoutProcessModel
{
    public static function getModel()
    {
        return EkomCheckoutProcess::inst()
            ->execute(function () {
                // a("all steps completed");
                XLog::debug("all steps completed");
            });

    }
}