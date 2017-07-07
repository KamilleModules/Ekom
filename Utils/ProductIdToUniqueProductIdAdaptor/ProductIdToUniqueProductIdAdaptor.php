<?php


namespace Module\Ekom\Utils\ProductIdToUniqueProductIdAdaptor;


use Module\Ekom\Api\EkomApi;

class ProductIdToUniqueProductIdAdaptor
{

    private $adaptors;


    public function __construct()
    {
        $this->adaptors = [];
    }

    public function getUniqueProductId($productId, $complementaryId = null)
    {
        $type = EkomApi::inst()->productLayer()->getProductTypeById($productId);
        if (array_key_exists($type, $this->adaptors)) {
            $adaptor = $this->adaptors[$type];
            return call_user_func($adaptor, $productId, $complementaryId);
        }
        return $productId;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function addAdaptor($productType, callable $adaptor)
    {
        $this->adaptors[$productType] = $adaptor;
        return $this;
    }
}