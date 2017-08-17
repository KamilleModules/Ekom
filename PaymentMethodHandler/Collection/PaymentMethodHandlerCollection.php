<?php


namespace Module\Ekom\PaymentMethodHandler\Collection;


use Module\Ekom\Exception\EkomException;
use Module\Ekom\PaymentMethodHandler\PaymentMethodHandlerInterface;

class PaymentMethodHandlerCollection implements PaymentMethodHandlerCollectionInterface
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


    public function setPaymentMethodHandler($name, PaymentMethodHandlerInterface $handler)
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

    public function get($name, $throwEx = true, $default = null)
    {
        if (array_key_exists($name, $this->configs)) {
            return $this->configs[$name];
        }
        if (true === $throwEx) {
            throw new EkomException("PaymentMethodHandler object not found with name: $name");
        }
        return $default;
    }


}