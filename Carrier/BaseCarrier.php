<?php


namespace Module\Ekom\Carrier;


abstract class BaseCarrier implements CarrierInterface
{

    protected $label;
    protected $name;
    protected $id;


    public function __construct()
    {
        $this->label = "";
        $this->name = "";
        $this->id = null;
    }

    public static function create()
    {
        return new static();
    }


    public function placeOrder(array &$orderModel, array $cartModel, array $orderData)
    {
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


}