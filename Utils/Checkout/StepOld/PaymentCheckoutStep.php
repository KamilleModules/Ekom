<?php


namespace Module\Ekom\Utils\Checkout\StepOld;



use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class PaymentCheckoutStep extends BaseCheckoutStep
{

    public static function create()
    {
        return new static();
    }

    public function listen(array &$doneData = null, array $defaults = [])
    {

        $configs = EkomApi::inst()->paymentLayer()->getPaymentMethodHandlers();
        return [
            "paymentMethodHandlers" => $configs,
            "uriPaymentThankYou" => E::link("Ekom_checkoutOnePageThankYou"),
        ];

    }


}