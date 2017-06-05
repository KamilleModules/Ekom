<?php



namespace Module\Ekom\Carrier;


interface CarrierInterface{


    /**
     * @param array $productInfos, an array of productInfo, each productInfo contains at least the following information:
     *
     *          - product_id: int, the product id
     *          - weight: number, the product's weight
     *
     *
     *
     * @param array $shippingAddress, information about the shipping address, it's an array with the following
     *          structure:
     *          - country: iso 639-3 code
     *          - : iso 639-3 code
     *
     *
     * @param array $rejected, array of id of rejected products (those which the carrier couldn't handle for some reasons)
     * @return float, the cost of the shipping of the accepted products
     *
     *
     * Note: if the carrier needs other information, it should use the ekomApi or other heuristics to get them.
     *
     */
    public function handleOrder(array $productInfos, array $shippingAddress, array &$rejected=[]);
}