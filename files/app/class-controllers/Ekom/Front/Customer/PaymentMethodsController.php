<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\Front\CustomerController;
use Core\Services\Hooks;
use Kamille\Utils\Claws\ClawsWidget;

class PaymentMethodsController extends CustomerController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();

        $claws = $this->getClaws();

        $claws
            ->setWidget("maincontent.paymentMethodsContainer", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/PaymentMethods/default")
            );
        /**
         * Each paymentMethod provider must provide a complete
         * interactive gui interface: an interface that allows the user
         * to handle/configure her payment method.
         *
         * The payment method providers can leverage this controller if
         * necessary.
         *
         */
        Hooks::call("Ekom_feedPaymentMethodsContainer", $claws);
    }
}