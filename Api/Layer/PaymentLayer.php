<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\X;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;

class PaymentLayer
{

    public function getPaymentMethodHandler($name)
    {

    }


    public function getSelectableItems()
    {
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $ret = [];
        $all = $coll->all();
        foreach ($all as $handler) {
            $ret[] = $handler->getSelectableItemModel();
        }
        return $ret;
    }

}