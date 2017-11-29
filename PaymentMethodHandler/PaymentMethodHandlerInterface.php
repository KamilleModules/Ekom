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
     * This method is called during a call to the ekom.placeOrder method: after it starts and before it ends.
     * You should use this step to make the payment transaction (if any) with external apis.
     *
     *
     */
//    public function placeOrder(array $orderModel);




}
