<?php


namespace Module\Ekom\PaymentMethodHandler\Collection;




use Module\Ekom\PaymentMethodHandler\PaymentMethodHandlerInterface;

interface PaymentMethodHandlerCollectionInterface
{

    /**
     * @param $name
     * @return PaymentMethodHandlerInterface
     */
    public function getPaymentMethodHandler($name);

    /**
     * @return PaymentMethodHandlerInterface[], array of payment method names => paymentMethodHandler instance
     */
    public function all();
}