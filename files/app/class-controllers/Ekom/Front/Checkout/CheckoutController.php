<?php


namespace Controller\Ekom\Front\Checkout;


use Controller\Ekom\EkomFrontController;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Model\Front\Checkout\EkomCheckoutProcessModel;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\Checkout\EkomCheckoutPageUtil;
use Module\Ekom\Utils\CheckoutProcess\EkomCheckoutProcess;
use Module\Ekom\Utils\E;


class CheckoutController extends EkomFrontController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();
        CurrentCheckoutData::set("checkoutType", "ekom");
        $model = EkomCheckoutProcessModel::getModel();
        $this->getClaws()
            ->setLayout('sandwich_1c/raw')
            ->setWidget("maincontent.checkout", ClawsWidget::create()
                ->setTemplate("Ekom/Checkout/default")
                ->setConf($model)
            );
    }


}