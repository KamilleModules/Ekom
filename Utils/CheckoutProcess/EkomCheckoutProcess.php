<?php


namespace Module\Ekom\Utils\CheckoutProcess;


use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Utils\CheckoutProcess\Step\Soko\SokoFirstShippingAddressCheckoutProcessStep;
use Module\Ekom\Utils\CheckoutProcess\Step\Soko\SokoLoginCheckoutProcessStep;
use Module\Ekom\Utils\CheckoutProcess\Step\Soko\SokoShippingCheckoutProcessStep;


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
        $this->addStep(SokoLoginCheckoutProcessStep::create(), "login");
        $this->addStep(SokoFirstShippingAddressCheckoutProcessStep::create(), "atLeastOneAddress");
        $this->addStep(SokoShippingCheckoutProcessStep::create(), "shipping");
    }

    protected function decorateModel(array &$model)
    {
        $model['cartModel'] = CartLayer::create()->getCartModel();
    }

}