<?php


namespace Module\Ekom\Carrier;


interface WithTrackingCarrierInterface extends CarrierInterface
{


    /**
     * @param array $orderInfo , same as CarrierInterface.getShippingCost's orderInfo
     * @return string|false, return either:
     *          - string: the tracking number
     *          - false in case of error, in which case the errors array is filled.
     *
     */
    public function getTrackingNumber(array $orderInfo, array $errors = []);
}