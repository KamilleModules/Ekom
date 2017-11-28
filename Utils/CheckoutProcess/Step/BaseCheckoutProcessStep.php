<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step;


abstract class BaseCheckoutProcessStep implements CheckoutProcessStepInterface
{


    protected $context;

    public function __construct()
    {
        $this->context = null;
    }

    public static function create()
    {
        return new static();
    }

    public function prepare(array $context)
    {
        $this->context = $context;
        return $this;
    }


}