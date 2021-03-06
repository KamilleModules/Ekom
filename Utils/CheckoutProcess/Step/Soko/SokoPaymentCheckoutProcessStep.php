<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;

class SokoPaymentCheckoutProcessStep extends BaseCheckoutProcessStep
{


    private $response;
    private $firstAddressForm;

    public function __construct()
    {
        parent::__construct();
        $this->response = null;
        $this->firstAddressForm = null;

    }


    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context)
    {
        /**
         * This step will never be valid.
         * Instead, use an ajax service to place the order.
         */
        return false;
    }

    public function isValid()
    {
        /**
         * This step will never be valid.
         * Instead, use an ajax service to place the order.
         */
        return false;
    }

    /**
     *
     * Set the data upfront for the payment step
     * ----------------------------------------------
     * This step is special.
     * Because it's the last step, and because steps are usually configured to
     * reflect the user's preferences, more often than not the gui user will directly
     * click on the "pay" button without specifically selecting/clicking on a gui button
     * (implicitly accepting the defaults).
     *
     * Therefore, the required CCD (CurrentCheckoutData) values: payment_method_id,
     * and, depending on the payment handler, potentially other values, will not be
     * injected into the CCD manually by the user (via the gui).
     *
     * Therefore, we do it programmatically by advance, and we ask/recommend the paymentMethodHandler
     * authors to feed the CCD as soon as their getModel method is called.
     * (which happens below when collecting paymentMethodItems).
     *
     * This design promotes the idea that when the user clicks the "pay" button,
     * all the relevant data are already in the CCD, which is semantically a more satisfying
     * approach than guessing afterwards the default values that the user might have wanted.
     *
     *
     */
    public function getModel()
    {


        $paymentMethodId = $this->getCurrentCheckoutData("payment_method_id");
        if (null === $paymentMethodId) {
            $paymentMethodId = PaymentLayer::getPreferredPaymentMethodId();
        }

        /**
         * @see SokoPaymentCheckoutProcessStep::getModel()
         */
        $this->setCurrentCheckoutData("payment_method_id", $paymentMethodId);


        return [
            "paymentMethodItems" => PaymentLayer::getPaymentMethodHandlersItems($paymentMethodId),
            "uriPaymentThankYou" => E::link("Ekom_checkoutOnePageThankYou"),
            "paymentMethodId" => $paymentMethodId,
        ];
    }
}