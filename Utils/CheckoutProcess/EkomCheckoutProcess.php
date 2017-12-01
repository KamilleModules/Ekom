<?php


namespace Module\Ekom\Utils\CheckoutProcess;


use Core\Services\Hooks;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Utils\CheckoutProcess\Step\Soko\SokoLoginCheckoutProcessStep;
use Module\Ekom\Utils\CheckoutProcess\Step\Soko\SokoPaymentCheckoutProcessStep;
use Module\Ekom\Utils\CheckoutProcess\Step\Soko\SokoShippingCheckoutProcessStep;
use Module\Ekom\Utils\E;


/**
 *
 * checkoutProcessModel
 * -------------------------
 * same as parent class, plus the following extra properties:
 * - cartModel: the cartModel defined at the top of the CartLayer class
 *
 *
 */
class EkomCheckoutProcess extends CheckoutProcess
{


    protected function init()
    {
        $cartModel = CartLayer::create()->getCartModel();


        if (false === E::userIsConnected()) {
            $this->addStep(SokoLoginCheckoutProcessStep::create(), "login", 100);
        }
        $this->addStep(SokoShippingCheckoutProcessStep::create(), "shipping", 200);
        $this->addStep(SokoPaymentCheckoutProcessStep::create(), "payment", 300);


        Hooks::call("Ekom_CheckoutProcess_decorateCheckoutProcess", $this, $cartModel);

    }

    protected function decorateModel(array &$model)
    {
        $model['cartModel'] = CartLayer::create()->getCartModel();
    }

}