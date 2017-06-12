<?php


namespace Module\Ekom;


class EkomServices
{


    protected static function Ekom_getCarrierCollection(){
        $c = \Module\Ekom\Carrier\Collection\CarrierCollection::create();
        \Core\Services\Hooks::call('Ekom_feedCarrierCollection', $c);
        return $c;
    }


    protected static function Ekom_getPaymentMethodHandlerCollection(){
        $c = \Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollection::create();
        \Core\Services\Hooks::call('Ekom_feedPaymentMethodHandlerCollection', $c);
        return $c;
    }
}


