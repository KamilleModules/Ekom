<?php


namespace Module\Ekom\CheckoutLayerProvider;


use Module\Ekom\Api\EkomApi;

class CheckoutLayerProvider
{

    private $getCheckoutLayerCallback;


    public function getCheckoutLayer()
    {
        if (null !== $this->getCheckoutLayerCallback) {
            $layer = call_user_func($this->getCheckoutLayerCallback);
            if (null !== $layer) {
                return $layer;
            }
        }
        return EkomApi::inst()->checkoutLayer();
    }


    public function setGetCheckoutLayerCallback(callable $getCheckoutLayerCallback)
    {
        $this->getCheckoutLayerCallback = $getCheckoutLayerCallback;
        return $this;
    }


}