<?php


namespace Module\Ekom\Carrier\Collection;


use Module\Ekom\Carrier\CarrierInterface;

class CarrierCollection implements CarrierCollectionInterface
{

    private $carriers;

    public function __construct()
    {
        $this->carriers = [];
    }

    public static function create()
    {
        return new static();
    }


    public function addCarrier($name, CarrierInterface $carrier)
    {
        $this->carriers[$name] = $carrier;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @param $name
     * @return CarrierInterface|false
     */
    public function getCarrier($name)
    {
        if (array_key_exists($name, $this->carriers)) {
            return $this->carriers[$name];
        }
        return false;
    }

    public function all()
    {
        return $this->carriers;
    }
}