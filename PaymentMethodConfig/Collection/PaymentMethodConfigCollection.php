<?php


namespace Module\Ekom\PaymentMethodConfig\Collection;


use Module\Ekom\PaymentMethodConfig\PaymentMethodConfigInterface;

class PaymentMethodConfigCollection implements PaymentMethodConfigCollectionInterface
{

    private $configs;

    public function __construct()
    {
        $this->configs = [];
    }

    public static function create()
    {
        return new static();
    }


    public function setPaymentMethodConfig($name, PaymentMethodConfigInterface $handler)
    {
        $this->configs[$name] = $handler;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function all()
    {
        return $this->configs;
    }
}