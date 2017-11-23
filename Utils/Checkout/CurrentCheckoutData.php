<?php


namespace Module\Ekom\Utils\Checkout;


use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Session\EkomSession;


/**
 * This class is responsible for holding the data collected during the checkout process.
 *
 * It's a central static registry sitting in the middle of modules, and modules
 * can communicate with it should they need to.
 *
 * It's unique in ekom (i.e. it's the reference class that you should use if you need
 * data about the current checkout process).
 *
 *
 */
class CurrentCheckoutData
{

    public static function isStarted()
    {
        return self::get('started');
    }

    public static function started()
    {
        self::getData(); // init
        self::set('started', true);
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

    public static function setMany(array $key2Value)
    {
        $data = self::getData();
        $data = array_replace($data, $key2Value);
        EkomSession::set("currentCheckoutData", $data);
    }

    public static function clean()
    {
        EkomSession::set("currentCheckoutData", null);
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getData()
    {
        $data = EkomSession::get("currentCheckoutData");
        if (null === $data) {
            $data = [
                "started" => false,
                "carrier" => CarrierLayer::getDefaultCarrier(),
                "shipping_address" => UserAddressLayer::getDefaultShippingAddress(),
                "billing_address" => UserAddressLayer::getDefaultBillingAddress(),
                "payment_method" => PaymentLayer::getDefaultPaymentMethod(),
            ];
//            Hooks::call("Ekom_Checkout_decorateCurrentCheckoutData", $data); // wait until u really need it
            EkomSession::set("currentCheckoutData", $data);
        }
        return $data;
    }
}