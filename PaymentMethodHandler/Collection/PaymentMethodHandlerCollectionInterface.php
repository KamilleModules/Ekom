<?php


namespace Module\Ekom\PaymentMethodHandler\Collection;


use Module\Ekom\PaymentMethodHandler\PaymentMethodHandlerInterface;

interface PaymentMethodHandlerCollectionInterface
{

    /**
     * @return PaymentMethodHandlerInterface[], array of payment method names => paymentMethodHandler instance
     */
    public function all();


    /**
     * @param $name
     * @param bool $throwEx
     * @param null $default
     * @return PaymentMethodHandlerInterface|mixed
     */
    public function get($name, $throwEx = true, $default = null);

}