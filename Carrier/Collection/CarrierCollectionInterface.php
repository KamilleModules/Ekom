<?php


namespace Module\Ekom\Carrier\Collection;


use Module\Ekom\Carrier\CarrierInterface;

interface CarrierCollectionInterface
{

    /**
     * @param $name
     * @return CarrierInterface
     */
    public function getCarrier($name);

    public function addCarrier($name, CarrierInterface $carrier);

    /**
     * @return CarrierInterface[], array of carrier names => carrier instance
     */
    public function all();
}