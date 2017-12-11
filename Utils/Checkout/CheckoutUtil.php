<?php


namespace Module\Ekom\Utils\Checkout;

use Core\Services\Hooks;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Model\Front\Checkout\EkomCheckoutProcessModel;
use Module\Ekom\Utils\CheckoutOrder\CheckoutOrderUtil;
use Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface;
use Module\Ekom\Utils\CheckoutProcess\EkomCheckoutProcess;
use Module\Ekom\Utils\E;


/**
 * Class CheckoutUtil
 * @package Module\Ekom\Utils\Checkout
 */
class CheckoutUtil
{

    /**
     * @return \Module\Ekom\Api\Layer\CartLayer|null
     * @throws \Kamille\Services\Exception\HooksException
     */
    public static function getCurrentCartLayer()
    {
        $cartLayer = null;
        Hooks::call("Ekom_CheckoutUtil_overrideCurrentCartLayer", $cartLayer);
        if (null !== $cartLayer) {
            return $cartLayer;
        }
        return EkomApi::inst()->cartLayer();
    }

    /**
     * @return EkomCheckoutProcess
     * @throws \Kamille\Services\Exception\HooksException
     */
    public static function getCurrentCheckoutProcess()
    {
        $checkoutProcess = null;
        Hooks::call("Ekom_CheckoutUtil_overrideCurrentCheckoutProcess", $checkoutProcess);
        if (null !== $checkoutProcess) {
            return $checkoutProcess;
        }
        return EkomCheckoutProcess::inst();
    }

    /**
     * @return array
     * @throws \Kamille\Services\Exception\HooksException
     */
    public static function getCurrentCheckoutProcessModel()
    {
        $checkoutProcess = null;
        Hooks::call("Ekom_CheckoutUtil_overrideCurrentCheckoutProcessModel", $checkoutProcessModel);
        if (null !== $checkoutProcessModel) {
            return $checkoutProcessModel;
        }
        return EkomCheckoutProcessModel::getModel();
    }

    /**
     * @return CheckoutOrderUtil
     * @throws \Kamille\Services\Exception\HooksException
     */
    public static function getCurrentCheckoutOrderUtil()
    {
        $checkoutProcess = null;
        Hooks::call("Ekom_CheckoutUtil_overrideCurrentCheckoutOrderUtil", $checkoutProcessModel);
        if (null !== $checkoutProcessModel) {
            return $checkoutProcessModel;
        }
        return CheckoutOrderUtil::create();
    }


    public static function getCheckoutThankYouRoute()
    {
        $checkoutThankYouRoute = null;
        Hooks::call("Ekom_CheckoutUtil_overrideCurrentCheckoutThankYouRoute", $checkoutThankYouRoute);
        if (null !== $checkoutThankYouRoute) {
            return $checkoutThankYouRoute;
        }
        return "Ekom_checkoutOnePageThankYou";
    }
}