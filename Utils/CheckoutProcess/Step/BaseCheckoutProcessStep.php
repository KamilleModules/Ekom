<?php


namespace Module\Ekom\Utils\CheckoutProcess\Step;


use Module\Ekom\Utils\Checkout\CurrentCheckoutData;

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

    public function isSkipped()
    {
        return false;
    }

    public function click()
    {

    }




    //--------------------------------------------
    // OVERRIDE ME
    //--------------------------------------------
    protected function setCurrentCheckoutData($key, $value)
    {
        CurrentCheckoutData::set($key, $value);
    }

    protected function getCurrentCheckoutData($key, $default = null)
    {
        return CurrentCheckoutData::get($key, $default);
    }

    protected function getCurrentCheckoutDataClass()
    {
        return "\Module\Ekom\Utils\Checkout\CurrentCheckoutData";
    }


}