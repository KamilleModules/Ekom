<?php


namespace Module\Ekom\Helper;


use Core\Services\Hooks;
use Ecp\EcpServiceUtil;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;

class CheckoutHelper
{
    public static function updateCurrentCheckoutData()
    {

        $changes = [];

        if (null !== ($carrier_id = EcpServiceUtil::get("carrier_id", false))) {
            CurrentCheckoutData::setCarrierId($carrier_id);
            $changes[] = 'carrier_id';
        }
        if (null !== ($shipping_address_id = EcpServiceUtil::get("shipping_address_id", false))) {
            CurrentCheckoutData::setShippingAddressId($shipping_address_id);
            $changes[] = 'shipping_address_id';
        }
        if (null !== ($billing_address_id = EcpServiceUtil::get("billing_address_id", false))) {
            CurrentCheckoutData::setBillingAddressId($billing_address_id);
            $changes[] = 'billing_address_id';
        }
        if (null !== ($payment_method_id = EcpServiceUtil::get("payment_method_id", false))) {
            CurrentCheckoutData::setPaymentMethodId($payment_method_id);
            $changes[] = 'payment_method_id';
        }

        if (null !== ($shop_address_id = EcpServiceUtil::get("shop_address_id", false))) {
            CurrentCheckoutData::setShopAddressId($shop_address_id);
            $changes[] = 'shop_address_id';
        }


        /**
         * @todo-ling: should be hooks handling the code below...
         */
        if (null !== ($billing_synced_with_shipping = EcpServiceUtil::get("billing_synced_with_shipping", false))) {
            CurrentCheckoutData::set("billing_synced_with_shipping", $billing_synced_with_shipping);
            $changes[] = 'billing_synced_with_shipping';
        }
        if (null !== ($comments = EcpServiceUtil::get("shipping_comments", false))) {
            CurrentCheckoutData::set("shipping_comments", $comments);
            $changes[] = 'shipping_comments';
        }
        if (null !== ($credit_card_payment_mode = EcpServiceUtil::get("credit_card_payment_mode", false))) {
            CurrentCheckoutData::set("credit_card_payment_mode", $credit_card_payment_mode);
            $changes[] = 'credit_card_payment_mode';
        }
        if (null !== ($creditCardId = EcpServiceUtil::get("credit_card_id", false))) {
            CurrentCheckoutData::set("credit_card_id", $creditCardId);
            $changes[] = 'credit_card_id';
        }


        Hooks::call("Ekom_CheckoutHelper_onUpdateCurrentCheckoutDataAfter", $changes);

    }
}