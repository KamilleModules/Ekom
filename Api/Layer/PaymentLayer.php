<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\X;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;

class PaymentLayer
{

    public function getPaymentMethodBlockModels()
    {
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $ret = [];
        $all = $coll->all();
        foreach ($all as $handler) {
            $ret[] = $handler->getPaymentMethodBlockModel();
        }
        return $ret;
    }


    public function getSelectableItemById($id)
    {
        $ret = null;
        $blocks = $this->getPaymentMethodBlockModels();
        $found = false;
        foreach ($blocks as $block) {
            $items = $block['items'];
            foreach ($items as $item) {
                if ($id === $item['id']) {
                    $ret = $item;
                    $found = true;
                    break;
                }
            }
        }
        if (false === $found) {
            throw new \Exception("No selectable item found with id $id");
        }
        return $ret;
    }

}