<?php


namespace Module\Ekom\Carrier;


use Module\Ekom\Exception\EkomException;

interface CarrierInterface
{


    /**
     * @param array $context , the context in which the shipping cost should be applied.
     *                It's an array with the following structure:
     *
     *      - cartItems: as defined at the top of CartLayer (cartModel.items)
     *      - ?cartWeight: number
     *      - ?shopAddress: array|null, a shopPhysicalAddress as defined at the top of the ShopLayer class
     *      - ?shippingAddress: array|null, the addressModel, as defined at the top of the UserAddressLayer class
     *
     *          Note: even if the user has no address, and/or the shop has no address, some carriers might use
     *              a flat rate. That explains why those properties are optional.
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
     * @throws EkomException if there is some missing data in the context, or another error
     *
     */
    public function getShippingInfo(array $context, array &$rejected = []);


    public function getLabel();
}