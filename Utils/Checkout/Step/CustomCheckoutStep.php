<?php


namespace Module\Ekom\Utils\Checkout\Step;


class CustomCheckoutStep extends BaseCheckoutStep
{

    private $returns;


    public function __construct()
    {
        parent::__construct();
        $this->returns = [
            'isSuccessfullyPosted' => true, // callback|bool
            'getFormModel' => [], // callback|array
            'getLabel' => "", // callback|string
            'getStepData' => "", // callback|array
        ];
    }

    public function setReturns(array $returns)
    {
        $this->returns = array_replace($this->returns, $returns);
        return $this;
    }


    public function prepare(array $stepData, array $context)
    {

    }

    public function isSuccessfullyPosted()
    {
        return $this->proxy('isSuccessfullyPosted');
    }


    public function getFormModel()
    {
        return $this->proxy('getFormModel');
    }

    public function getLabel()
    {
        return $this->proxy('getLabel');
    }

    public function getStepData()
    {
        return $this->proxy('getStepData');
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function proxy($key)
    {
        $return = $this->returns[$key];
        if (is_callable($return)) {
            $return = call_user_func($return);
        }
        return $return;
    }

}