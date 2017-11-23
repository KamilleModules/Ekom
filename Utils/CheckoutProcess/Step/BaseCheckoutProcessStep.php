<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step;


abstract class BaseCheckoutProcessStep implements CheckoutProcessStepInterface
{

    public function __construct()
    {
        //
    }

    public static function create()
    {
        return new static();
    }

}