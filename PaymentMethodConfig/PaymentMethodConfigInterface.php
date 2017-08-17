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

    /**
     *
     * Parse the configuration from the database and returns a reliable set of default options.
     * An option is an something that the user must configure in order to place a successful order.
     *
     * NOTE THAT IF AN OPTION IS BADLY CONFIGURED, THE PLACEORDER METHOD SHALL FAIL !!
     *
     * In fact, this method ensures that the database configuration is ok, and if not provides fallback options.
     *
     *
     * @param $configuration , the configuration data stored in the database (ek_shop_has_payment_method).
     * @return array of key => value representing the default options to use for this payment method.
     *
     */
    public function getDefaultOptions($configuration = null);
}
