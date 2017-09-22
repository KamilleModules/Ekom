<?php


namespace Module\Ekom\Utils\Stepper\Step;


class Step implements StepInterface
{

    protected $data;

    public function __construct()
    {
        $this->data = [];
    }

    public static function create()
    {
        return new static();
    }

    /**
     * The data is injected into this step, and should be used to display
     * the initial state with the values that the user typed earlier
     * (emulating user data persistency)
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array the data to store in the stepper
     */
    public function getData()
    {
        return $this->data;
    }

    public function render()
    {

    }

    public function isPosted()
    {
        return false;
    }

    public function isValid()
    {
        return false;
    }


}