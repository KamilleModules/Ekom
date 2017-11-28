<?php


namespace Module\Ekom\Utils\CheckoutProcess;


/**
 *
 * @link https://github.com/KamilleModules/Ekom/tree/master/doc/checkout/checkout-process.md
 * Note: this is a singleton.
 *
 */
interface CheckoutProcessInterface
{


    public function setShippingAddressId($id);

    public function setBillingAddressId($id);

    public function setPaymentMethodId($id);

    public function setCarrierId($id);


    public function set($key, $value);

    public function get($key, $default = null, $throwEx = false);
}