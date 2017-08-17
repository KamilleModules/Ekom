<?php


namespace Module\Ekom\PaymentMethodHandlerOld\Collection;



use Module\Ekom\PaymentMethodHandlerOld\PaymentMethodHandlerInterface;

class PaymentMethodHandlerCollection implements PaymentMethodHandlerCollectionInterface
{

    private $paymentMethodHandlers;

    public function __construct()
    {
        $this->paymentMethodHandlers = [];
    }

    public static function create()
    {
        return new static();
    }


    public function addPaymentMethodHandler($name, PaymentMethodHandlerInterface $handler)
    {
        $this->paymentMethodHandlers[$name] = $handler;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @param $name
     * @return PaymentMethodHandlerInterface|false
     */
    public function getPaymentMethodHandler($name)
    {
        if (array_key_exists($name, $this->paymentMethodHandlers)) {
            return $this->paymentMethodHandlers[$name];
        }
        return false;
    }

    public function all()
    {
        return $this->paymentMethodHandlers;
    }
}