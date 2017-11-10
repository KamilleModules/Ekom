<?php


namespace Module\Ekom\Carrier;


interface CarrierInterface
{


    /**
     * @param array $orderInfo , the context in which the shipping cost should be applied.
     *                It's an array with the following structure:
     *
     *      - products: an array of boxModel
     *      - ?shopAddress: array|null, a shopPhysicalAddress as defined at the top of the ShopLayer class
     *      - ?shippingAddress: array|null, the addressModel, as defined at the top of the UserAddressLayer class
     *
     *
     * @param array $rejected , array of productInfo representing the rejected products (those which the carrier couldn't handle)
     *
     *
     * @return array, an array with the following info:
     *      - shipping_cost: float, the cost of the shipping of the accepted products
     *      - ?estimated_delivery_date: datetime|null, the estimated delivery date or null if it cannot be estimated
     *
     *
     *
     */
    public function getShippingCost(array $orderInfo, array &$rejected = []);


    public function getLabel();
}