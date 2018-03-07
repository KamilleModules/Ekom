<?php


namespace Controller\Ekom\Front\Checkout;


use Controller\Ekom\EkomFrontController;
use Kamille\Ling\Z;
use Kamille\Utils\Laws\Config\LawsConfig;


class CheckoutMultipleAddressController extends EkomFrontController
{

    public function render()
    {
        $step = Z::getUrlParam("step");
        return $this->renderByViewId("Ekom/checkout/checkoutMultiple-$step");
    }
}