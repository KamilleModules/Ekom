<?php


namespace Module\Ekom\PaymentMethodHandler;


interface PaymentMethodHandlerInterface
{
    /**
     * @return array, the paymentMethodBlock model
     *
     * The model structure depends on the concrete class;
     * however it must contain at least the following keys:
     *
     * - label:
     * - type: the type of the payment block
     * - ?panel: the model for a configuration panel if any
     *
     */
    public function getPaymentMethodBlockModel();
}