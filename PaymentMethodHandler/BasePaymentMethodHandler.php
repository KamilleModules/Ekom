<?php


namespace Module\Ekom\PaymentMethodHandler;


abstract class BasePaymentMethodHandler implements PaymentMethodHandlerInterface
{

    public static function create()
    {
        return new static();
    }

    public function placeOrder(array &$orderModel, array $cartModel, array $orderData)
    {

    }


}
