<?php


namespace Module\Ekom\Helper;


use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;

class CheckoutProcessHelper
{
    public static function fixUnsyncedCurrentCheckoutDataAddresses(string $currentCheckoutDataClass = null)
    {


        if (true === E::userIsConnected()) {

            $userId = E::getUserId();
            if (null === $currentCheckoutDataClass) {
                $currentCheckoutDataClass = "\Module\Ekom\Utils\Checkout\CurrentCheckoutData";
            }
            /**
             * Now it's possible that the CurrentCheckoutData shippingAddress/billingAddress
             * get out of sync with the actual user addresses.
             *
             * This happened to me in the following case:
             * - I removed my last address (using the gui, why do they permit that?)
             * - the gui prompts me immediately with a form to create a new address, which I fill
             * - after validating the form, the CurrentCheckoutData address_id doesn't exist anymore.
             *
             * The code below fixes that.
             */
            $ccdBillingAddressId = call_user_func([$currentCheckoutDataClass, "getBillingAddressId"]);
            $ccdShippingAddressId = call_user_func([$currentCheckoutDataClass, "getShippingAddressId"]);
            if (false === UserAddressLayer::userOwnsAddress($userId, $ccdShippingAddressId)) {
                $preferredShippingAddress = UserAddressLayer::getPreferredShippingAddress($userId);
                $preferredShippingAddressId = $preferredShippingAddress['address_id'];
                call_user_func([$currentCheckoutDataClass, "setShippingAddressId"], $preferredShippingAddressId);
            }

            if (false === UserAddressLayer::userOwnsAddress($userId, $ccdBillingAddressId)) {
                $preferredBillingAddress = UserAddressLayer::getPreferredBillingAddress($userId);
                $preferredBillingAddressId = $preferredBillingAddress['address_id'];
                call_user_func([$currentCheckoutDataClass, "setBillingAddressId"], $preferredBillingAddressId);
            }
        }
    }
}