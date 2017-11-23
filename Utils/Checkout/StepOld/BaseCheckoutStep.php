<?php


namespace Module\Ekom\Utils\Checkout\StepOld;





abstract class BaseCheckoutStep implements CheckoutStepInterface
{

    public static function create()
    {
        return new static();
    }
}