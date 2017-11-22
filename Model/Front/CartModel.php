<?php


namespace Module\Ekom\Model\Front;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class CartModel
{
    public static function getModel(){
        return [
            "cart" => EkomApi::inst()->cartLayer()->getCartModel(),
            "uriCheckout" => E::link("Ekom_checkoutOnePage"),
        ];
    }

}