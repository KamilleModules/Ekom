<?php


namespace Module\Ekom;


class EkomServices
{


    protected static function Ekom_getCarrierCollection()
    {
        $c = \Module\Ekom\Carrier\Collection\CarrierCollection::create();
        \Core\Services\Hooks::call('Ekom_feedCarrierCollection', $c);
        return $c;
    }


    protected static function Ekom_getPaymentMethodHandlerCollection()
    {
        $c = \Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollection::create();
        \Core\Services\Hooks::call('Ekom_feedPaymentMethodHandlerCollection', $c);
        return $c;
    }


    protected static function Ekom_getProductPriceChain()
    {
        $c = \Module\Ekom\Price\PriceChain\EkomProductPriceChain::create();
        \Core\Services\Hooks::call('Ekom_feedEkomProductPriceChain', $c);
        return $c;
    }

    protected static function Ekom_getCartPriceChain()
    {
        $c = \Module\Ekom\Price\PriceChain\EkomCartPriceChain::create();
        \Core\Services\Hooks::call('Ekom_feedEkomCartPriceChain', $c);
        return $c;
    }

    protected static function Ekom_getTotalPriceChain()
    {
        $c = \Module\Ekom\Price\PriceChain\EkomTotalPriceChain::create();
        \Core\Services\Hooks::call('Ekom_feedEkomTotalPriceChain', $c);
        return $c;
    }


    protected static function Ekom_jsApiLoader(){
        $l = new \Module\Ekom\JsApiLoader\EkomJsApiLoader();
        \Core\Services\Hooks::call('Ekom_feedJsApiLoader', $l);
        return $l;
    }
}



