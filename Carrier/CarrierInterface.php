<?php


namespace Module\Ekom\Carrier;


interface CarrierInterface
{


    /**
     *
     * Return the shippingInfoModel array corresponding to the given context,
     * or false if the carrier is unable to compute this array correctly (probably because some
     * info are missing in the context)
     *
     * @see EkomModels::shippingInfoModel().
     *
     *
     * @param array :shippingContextModel $context
     * @see EkomModels::shippingContextModel().
     *
     *
     * @return false|array:shippingInfoModel
     * @see EkomModels::shippingInfoModel().
     *
     *
     *
     * Note: there was a rejected second argument to this method before,
     * but for now I've dropped it.
     * The idea was that a carrier might handle SOME of the products in the cart but not all.
     *
     *
     *
     */
    public function getShippingInfo(array $context);


    public function getLabel();

    public function setName($name);

    public function getName();

    public function setId($id);

    public function getId();
}