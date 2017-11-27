<?php


namespace Module\Ekom\Utils\Checkout;


use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Session\EkomSession;


/**
 *
 * This class is just a dumb store (i.e. no logic).
 * Heuristics are in the CheckoutLayer (i.e. if you want the relevant shipping address id to apply
 * to your cart for instance).
 *
 * @link https://github.com/KamilleModules/Ekom/tree/master/doc/checkout/checkout-placeorder-and-currentcheckoutdata.md
 *
 * This class is responsible for collecting the data necessary to complete the checkout process (i.e. place the order).
 * Once the order is placed, the data is flushed and the process starts over again.
 *
 *
 * It's a central static registry sitting in the middle of modules, and so modules
 * can communicate with it should they need to.
 *
 *
 * currentCheckoutDataModel
 * =========================
 *
 * - started: bool, whether or not the session has started.
 *              Once started, data stays for ever until you clean them manually
 *              with the clean method (you generally call this method at the very end
 *              of a successful checkout process).
 * - carrier_id: int|null, the id of the carrier
 * - shipping_address_id: int|null, the id of the shipping address
 * - billing_address_id: int|null, the id of the billing address
 * - payment_method_id: int|null, the id of the payment method
 * - ...your own
 *
 *
 *
 */
class CurrentCheckoutData
{


    public static function getCarrierId()
    {
        return self::get("carrier_id", null);
    }

    public static function getShippingAddressId()
    {
        return self::get("shipping_address_id", null);
    }

    public static function get($key, $default = null)
    {
        $data = self::getData();
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
        return $default;
    }

    public static function set($key, $value)
    {
        $data = self::getData();
        $data[$key] = $value;
        EkomSession::set("currentCheckoutData", $data);
    }

//    public static function setMany(array $key2Value)
//    {
//        $data = self::getData();
//        $data = array_replace($data, $key2Value);
//        EkomSession::set("currentCheckoutData", $data);
//    }

    public static function clean()
    {
        EkomSession::set("currentCheckoutData", null);
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getData()
    {
        return EkomSession::get("currentCheckoutData", []);
    }
}