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
     * - type: the type of the payment block
     * - items: an array containing selectable items.
     *          Each item contains at least the following keys:
     *
     *          - id: an unique identifier to identify the selectable item
     *          - type: the type of the selectable item
     *
     */
    public function getPaymentMethodBlockModel();
}