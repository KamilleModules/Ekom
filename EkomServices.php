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


    protected static function Ekom_jsApiLoader()
    {
        $l = new \Module\Ekom\JsApiLoader\EkomJsApiLoader();
        \Core\Services\Hooks::call('Ekom_feedJsApiLoader', $l);
        return $l;
    }

    protected static function Ekom_notifier()
    {
        $o = new \Module\Ekom\Notifier\EkomNotifier();
        \Core\Services\Hooks::call('Ekom_feedEkomNotifier', $o);
        return $o;
    }




    protected static function Ekom_statusProviderCollection()
    {
        $o = new \Module\Ekom\Status\ProviderCollection\EkomStatusProviderCollection();
        \Core\Services\Hooks::call('Ekom_feedStatusProviderCollection', $o);
        return $o;
    }

    protected static function Ekom_statusProvider()
    {
        /**
         * @var \Module\Ekom\Status\ProviderCollection\EkomStatusProviderCollection $coll
         */
        $coll = \Core\Services\X::get("Ekom_statusProviderCollection");
        $all = $coll->all();
        $key = \Module\Ekom\Utils\E::conf("statusProvider");
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }
        throw new \Module\Ekom\Exception\EkomException("statusProvider not configured for the current shop");
    }


    protected static function Ekom_dynamicWidgetBinder()
    {
        $o = \Kamille\Utils\Laws\DynamicWidgetBinder\DynamicWidgetBinder::create();
        \Core\Services\Hooks::call("Ekom_feedDynamicWidgetBinder", $o);
        return $o;
    }


}



