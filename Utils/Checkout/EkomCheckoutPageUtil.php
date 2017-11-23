<?php


namespace Module\Ekom\Utils\Checkout;


use Core\Services\Hooks;
use Module\Ekom\Utils\Checkout\Step\LoginCheckoutStep;
use Module\Ekom\Utils\Checkout\Step\PaymentCheckoutStep;
use Module\Ekom\Utils\Checkout\Step\ShippingCheckoutStep;

/**
 *
 *
 * //--------------------------------------------
 * // checkoutPageModel
 * //--------------------------------------------
 * - cartModel: the cartModel, defined at the top of CartLayer
 * - checkoutModel: (a model for a visual representation of a one page checkout)
 *      - model: free form array containing
 *
 *
 *
 *
 */
class EkomCheckoutPageUtil extends CheckoutPageUtil
{

    protected $useTraining;
    protected $usePayment;
    protected $useEvent;



    public function __construct()
    {
        parent::__construct();

        $this->useTraining = true;
        $this->usePayment = true;
        $this->useEvent = true;




        Hooks::call("Ekom_CheckoutPageUtil_registerSteps", $this);
    }


    public function getModel(array $context = null){


        /**
         * Those positions (0,1000,2000) are my defaults.
         * If you use this class, you can assume that those numbers won't change.
         */
        $this->registerStep("login", LoginCheckoutStep::create(), 1000);
        $this->registerStep("shipping", ShippingCheckoutStep::create(), 2000);
//        $this->registerStep("payment", PaymentCheckoutStep::create(), 3000);

        return parent::getModel($context);
    }

}