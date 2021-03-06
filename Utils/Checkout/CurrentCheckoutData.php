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
 * - carrier_id: int|null, the id of the carrier
 * - shipping_address_id: int|null, the id of the shipping address
 * - billing_address_id: int|null, the id of the billing address
 * - payment_method_id: int|null, the id of the payment method
 * - ...your own
 *
 *
 * Note about security
 * ========================
 * Notice that this class don't to any checking about the user identity.
 * So be careful when displaying confidential information: you should always ensure
 * that the user is really who she/he pretends to be.
 *
 */
class CurrentCheckoutData
{
    protected static $namespace = "currentCheckoutData";


    public static function getCarrierId()
    {
        return static::get("carrier_id", null);
    }

    public static function setCarrierId($id)
    {
        static::set("carrier_id", $id);
    }

    public static function getShippingAddressId()
    {

        return static::get("shipping_address_id", null);
    }

    public static function setShippingAddressId($id)
    {
        static::set("shipping_address_id", $id);
    }

    public static function getBillingAddressId()
    {
        return static::get("billing_address_id", null);
    }

    public static function setBillingAddressId($id)
    {
        static::set("billing_address_id", $id);
    }

    public static function getStoreAddressId()
    {
        return static::get("store_address_id", null);
    }

    public static function setStoreAddressId($id)
    {
        static::set("store_address_id", $id);
    }

    public static function getPaymentMethodId()
    {
        return static::get("payment_method_id", null);
    }

    public static function setPaymentMethodId($id)
    {
        static::set("payment_method_id", $id);
    }


    public static function get($key, $default = null)
    {
        $data = static::all();
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
        return $default;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public static function all()
    {
        return EkomSession::get(static::$namespace, []);
    }

    public static function set($key, $value)
    {
        $data = self::all();
        $data[$key] = $value;
        EkomSession::set(static::$namespace, $data);
    }

    public static function remove($key)
    {
        $data = self::all();
        unset($data[$key]);
        EkomSession::set(static::$namespace, $data);
    }

//    public static function setMany(array $key2Value)
//    {
//        $data = self::getData();
//        $data = array_replace($data, $key2Value);
//        EkomSession::set("currentCheckoutData", $data);
//    }

    public static function clean()
    {
        EkomSession::set(static::$namespace, []);
    }
}