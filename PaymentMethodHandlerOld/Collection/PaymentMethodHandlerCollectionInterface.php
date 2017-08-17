<?php


namespace Module\Ekom\PaymentMethodHandlerOld\Collection;




use Module\Ekom\PaymentMethodHandlerOld\PaymentMethodHandlerInterface;

interface PaymentMethodHandlerCollectionInterface
{

    /**
     * @param $name
     * @return PaymentMethodHandlerInterface
     */
    public function getPaymentMethodHandler($name);

    /**
     * @return PaymentMethodHandlerInterface[], array of payment method names => PaymentMethodHandlerOld instance
     */
    public function all();
}