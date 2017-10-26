<?php


namespace Module\Ekom\Utils\Checkout\Step;





abstract class BaseCheckoutStep implements CheckoutStepInterface
{

    public static function create()
    {
        return new static();
    }
}