<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step\Soko;


use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\SokoForm\UserAddress\UserAddressSokoForm;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\Step\BaseCheckoutProcessStep;
use Module\Ekom\Utils\E;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoFormInterface;

class SokoPaymentCheckoutProcessStep extends BaseCheckoutProcessStep
{


    private $response;
    private $shopId;
    private $langId;
    private $firstAddressForm;

    public function __construct()
    {
        parent::__construct();
        $this->response = null;
        $this->shopId = null;
        $this->langId = null;
        $this->firstAddressForm = null;

    }


    public function isPostedSuccessfully(CheckoutProcessInterface $cp, array $context)
    {
        $this->shopId = $context['shop_id'];
        return false;
    }

    public function isValid()
    {
        return false;
    }

    public function getModel()
    {
        return [
            "paymentMethodItems" => PaymentLayer::getPaymentMethodHandlersItems($this->shopId),
            "uriPaymentThankYou" => E::link("Ekom_checkoutOnePageThankYou"),
        ];
    }
}