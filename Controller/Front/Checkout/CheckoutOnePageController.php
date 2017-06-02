<?php


namespace Controller\Ekom\Front\Checkout;


use Controller\Ekom\EkomFrontController;
use Kamille\Utils\Laws\Config\LawsConfig;


class CheckoutOnePageController extends EkomFrontController
{

    public function render()
    {
        $step = (array_key_exists('step', $_GET)) ? $_GET['step'] : 1;

        return $this->renderByViewId("Ekom/checkout/checkoutOnePage", LawsConfig::create()->replace([
            'widgets' => [
                'checkoutOnePage' => [
                    'tpl' => "Front/Checkout/CheckoutOnePage/prototype" . $step,
                ],
            ],
        ]));
    }
}