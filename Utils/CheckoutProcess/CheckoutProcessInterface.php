<?php


namespace Module\Ekom\Utils\CheckoutProcess;

use Module\Ekom\Utils\CheckoutProcess\Step\CheckoutProcessStepInterface;


/**
 *
 * @link https://github.com/KamilleModules/Ekom/tree/master/doc/checkout/checkout-process.md
 * Note: this is a singleton.
 *
 */
interface CheckoutProcessInterface
{

    public function addStep(CheckoutProcessStepInterface $step, $name = null, $position = 0);

    public function set($key, $value);

    public function get($key, $default = null, $throwEx = false);
}