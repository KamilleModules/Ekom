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
//    public function placeOrder(array &$orderModel, array $cartModel, array $orderData);


    /**
     * @param $orderData : array:<orderDataModel>
     * @see EkomModels::orderDataModel()
     * @param $cartModel : array:<cartModel>
     * @see EkomModels::cartModel()
     *
     * @return array, the payment configuration details when the user clicked the "pay" button.
     * Those details appear on the invoice and/or at the order level.
     * Ex:
     *      payment method: credit card
     *      payment mode: 3x sans frais
     *      payment schedule:
     *          - [ 2017-12-03, "1er versement", 100, "100€" ]
     *          - [ 2018-01-03, "2ème versement", 100, "100€" ]
     *          - [ 2018-02-03, "3ème versement", 100, "100€" ]
     *
     */
    public function getCommittedConfiguration(array $orderData, array $cartModel);


}
