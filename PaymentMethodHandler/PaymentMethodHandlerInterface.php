<?php


namespace Module\Ekom\PaymentMethodHandler;


interface PaymentMethodHandlerInterface
{


    /**
     * @return array, the model used to display the payment item in a gui.
     * (during the checkout process, or the user account, or elsewhere...).
     *
     */
    public function getModel();


    /**
     * If the handler needs to communicate with external apis to get some kind of "financial transaction identifier",
     * this is where it happens.
     *
     * The transaction identifier is then appended to the orderModel, using the key:
     * - pay_identifier
     *
     * Also, we can pass an array of parameters using the key:
     *
     * - payment_method_details
     *
     *
     *
     * @param array $orderModel
     * @see EkomModels::orderModel()
     * @param array $cartModel
     * @see EkomModels::cartModel()
     * @param array $orderData , the data collected during the checkout process
     * @see CheckoutOrderUtil::placeOrder()
     *
     * @return void
     */
    public function placeOrder(array &$orderModel, array $cartModel, array $orderData);




}
