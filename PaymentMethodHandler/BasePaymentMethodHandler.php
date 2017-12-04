<?php


namespace Module\Ekom\PaymentMethodHandler;


use Module\Ekom\Exception\EkomException;

abstract class BasePaymentMethodHandler implements PaymentMethodHandlerInterface
{

    public static function create()
    {
        return new static();
    }

    public function placeOrder(array &$orderModel, array $cartModel, array $orderData)
    {

    }


    public function getCommittedConfiguration(array $orderData, array $cartModel)
    {
        return [];
    }

    /**
     * @param $msg
     * @throws EkomException
     */
    protected function error($msg)
    {
        throw new EkomException($msg);
    }
}
