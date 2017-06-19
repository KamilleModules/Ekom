<?php


namespace Module\Ekom\Carrier;


interface CarrierInterface
{


    /**
     * @param array $orderInfo , array with the following structure;
     *
     *      - ?forReal: bool=false, whether or not to do a real order handling or just an estimate.
     *                  If it's for real, the carrier might communicate with external apis
     *                  and retrieve a real world tracking number for instance.
     *                  If it's fake, all communication with external apis is shut down.
     *
     *
     *      - products: an array of productInfo, each productInfo contains at least the following information:
     *
     *          - product_id: int, the product id
     *          - weight: number, the product's weight
     *      - ?shopAddress, information about the shop address (or at least the address whence the products are sent).
     *              structure:
     *              - country: iso 639-3 code
     *              - postcode: a post code number helping identifying the town
     *              - ?city: the city label
     *      - ?shippingAddress, information about the shipping address, it's an array with the same structur as the shop address.
     *
     *
     * @param array $rejected , array of id of rejected products (those which the carrier couldn't handle)
     *
     *
     * @return array, an array with the following info:
     *      - shipping_cost: float, the cost of the shipping of the accepted products
     *      - ?estimated_delivery_date: datetime|null, the estimated delivery date or null if it cannot be estimated
     *      - ?tracking_number: string, (if the carrier allows it)
     *
     *
     * Note: if the carrier needs other information, it should use the ekomApi or other heuristics to get them.
     *
     */
    public function handleOrder(array $orderInfo, array &$rejected = []);

    public function getLabel();
}