<?php


namespace Module\Ekom\Carrier;


use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\CheckoutOrder\CheckoutOrderUtil;

interface CarrierInterface
{


    /**
     *
     * Return the shippingInfoModel array corresponding to the given context,
     * or false if the carrier is unable to compute this array correctly at the moment
     * (probably because some info are missing in the context)
     *
     * @see EkomModels::shippingInfoModel()
     *
     */
    public function getShippingInfo(array $context, string &$errorCode = null);

    /**
     * If the carrier needs to communicate with external apis to get a tracking number,
     * this is where it happens.
     * The tracking number needs then to be appended to the orderModel, using the key:
     * - tracking_number
     *
     * Also, we can pass an array of parameters using the key:
     *
     * - carrier_details
     *
     *
     *
     * @param array $orderModel
     * @see EkomModels::orderModel()
     * @param array $cartModel
     * @see EkomModels::cartModel()
     * @param array $orderData , the data collected during the checkout process
     * @see CheckoutOrderUtil::placeOrder()
     *
     * @return mixed
     */
    public function placeOrder(array &$orderModel, array $cartModel, array $orderData);


    public function getLabel();

    public function setName($name);

    public function getName();

    public function setId($id);

    public function getId();
}