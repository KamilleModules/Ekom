<?php


namespace Module\Ekom\PaymentMethodHandler;

/**
 *
 * Helps accessing the configuration of a payment method.
 * There are many potential subtleties to a payment method's configuration,
 * hence the different methods.
 *
 *
 * Basically:
 *
 * - getConfig: helps defining which internal steps are used in the checkout process during the payment phase
 * - getDefaultOptions:
 *
 *                  The goal is that when the gui of the payment step of the checkout page
 *                  is displayed for the first time, it's synced with the values in session
 *                  (provided by the getDefaultOptions method),
 *                  so that if the user validates the form without interacting
 *                  with the gui (since the session values are used by the code),
 *                  the session values and the gui tell the same story.
 *
 *
 *                  For instance, in the case of the card wallet, the user can choose between different
 *                  cards, and so a default card might be chosen to start with.
 *                  The default options are placed in the session, so that they are immediately available.
 *
 *                  (if the user doesn't click anything on the gui, the default options will prevail
 *                  instead of no value).
 *
 *                  In fact, this is the true reason why the defaultOptions were created in the first place;
 *                  if the user doesn't click anything but the pay button, the placeOrder method
 *                  uses the default options (which the gui should reflect when displayed for the "first time")
 *
 *
 *
 */
interface PaymentMethodHandlerInterface
{


    /**
     * Contextual configuration of a payment method.
     *
     *
     * The configuration of a payment method can depend on the items in the cart for instance.
     *
     * For instance, a credit card payment method can propose recurrent options
     * only if the cart contains more than a certain amount of money.
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

    /**
     * Check that the payment info are correct,
     * then pays,
     * then return an useful array of info about the payment transaction.
     *
     *
     *
     * @param $orderModel , the order model, with the extra properties added:
     *          - ?reference: the order reference
     *                      required by certain handlers
     *
     *
     * @return array, an array of useful information about the successful transaction.
     *                      If the key pay_id is returned, then it will be used as the pay_identifier by ekom
     * @throws \Exception if something goes wrong
     *
     */
    public function pay(array $extendedOrderModel);
}
