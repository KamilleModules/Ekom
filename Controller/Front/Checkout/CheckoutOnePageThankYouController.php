<?php


namespace Controller\Ekom\Front\Checkout;


use Controller\Ekom\EkomFrontController;
use Kamille\Utils\Laws\Config\LawsConfig;


class CheckoutOnePageThankYouController extends EkomFrontController
{

    public function render()
    {
        return $this->renderByViewId("Ekom/checkout/checkoutOnePageThankYou");
    }
}