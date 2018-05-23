<?php


namespace Module\Ekom\Utils\CheckoutProcess;


use Core\Services\Hooks;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutProcess\Step\Soko\SokoBillingCheckoutProcessStep;
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

    private $_extendedCartModel;

    protected function init()
    {

        $this->debug("EkomCheckoutProcess: init");
        $this->_extendedCartModel = CartLayer::create()->getExtendedCartModel();

        if (true || false === E::userIsConnected()) {
            $this->addStep(SokoLoginCheckoutProcessStep::create(), "login", 100);
        }
        if ($this->_extendedCartModel['cart']['cart_total_weight'] > 0) {
            $this->addStep(SokoShippingCheckoutProcessStep::create(), "shipping", 200);
        } else {
            $this->addStep(SokoBillingCheckoutProcessStep::create(), "billing", 200);
        }
        $this->addStep(SokoPaymentCheckoutProcessStep::create(), "payment", 300);


        Hooks::call("Ekom_CheckoutProcess_decorateCheckoutProcess", $this, $this->_extendedCartModel);

    }

    protected function decorateModel(array &$model)
    {
        $model['cartModel'] = $this->_extendedCartModel;
    }

}