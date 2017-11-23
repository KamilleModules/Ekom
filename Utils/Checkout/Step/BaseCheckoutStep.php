<?php


namespace Module\Ekom\Utils\Checkout\Step;


abstract class BaseCheckoutStep implements CheckoutStepInterface
{

    protected $label;
    protected $stepData;
    protected $context;

    public function __construct()
    {
        $this->label = "";
        $this->stepData = [];
        $this->context = [];
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
        return $this->stepData;
    }

    public function prepare(array $stepData, array $context)
    {
        $this->stepData = $stepData;
        $this->context = $context;

    }

}