<?php


namespace Module\Ekom\PaymentMethodConfig\Collection;


use Module\Ekom\PaymentMethodConfig\PaymentMethodConfigInterface;

interface PaymentMethodConfigCollectionInterface
{

    /**
     * @return PaymentMethodConfigInterface[], array of payment method names => paymentMethodConfig instance
     */
    public function all();

}