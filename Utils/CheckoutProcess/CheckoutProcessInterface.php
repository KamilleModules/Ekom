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

    public function set($key, $value);

    public function get($key, $default = null, $throwEx = false);
}