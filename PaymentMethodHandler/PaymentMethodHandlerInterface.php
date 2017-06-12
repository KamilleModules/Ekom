<?php


namespace Module\Ekom\PaymentMethodHandler;


interface PaymentMethodHandlerInterface
{
    /**
     * @return array, the model for a selectable item
     */
    public function getSelectableItemModel();
}