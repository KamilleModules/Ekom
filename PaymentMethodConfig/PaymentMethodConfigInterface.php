<?php


namespace Module\Ekom\PaymentMethodConfig;


/**
 * Dynamic configuration of a payment method.
 *
 * The configuration of a payment method can depend on the items in the cart.
 *
 * For instance, a credit card payment method can propose recurrent options
 * if the cart contains more than a certain amount of money.
 *
 *
 * Important note:
 * It does not remember user choices, it just takes care of the payment
 * method "business" configuration.
 *
 * To access user choices, please use the CheckoutLayer.setPaymentMethod
 * method.
 * (this is actually a very important remark for that leads towards an ekom clean design)
 *
 *
 */
interface PaymentMethodConfigInterface
{

    /**
     * @return array
     */
    public function getConfig();
}
