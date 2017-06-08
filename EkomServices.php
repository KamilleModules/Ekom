<?php


namespace Module\Ekom;


class EkomServices
{


    protected static function Ekom_getCarrierCollection(){
        $c = \Module\Ekom\Carrier\Collection\CarrierCollection::create();
        \Core\Services\Hooks::call('Ekom_feedCarrierCollection', $c);
        return $c;
    }
}


