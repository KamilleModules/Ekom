<?php


namespace Module\Ekom\Utils\Checkout\Step;


abstract class BaseCheckoutStep implements CheckoutStepInterface
{

    protected $label;
    protected $stepsData;

    public function __construct()
    {
        $this->label = "";
        $this->stepsData = [];
    }

    public static function create()
    {
        return new static();
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getStepData()
    {
        return $this->stepsData;
    }

}