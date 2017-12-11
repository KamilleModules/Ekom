<?php


namespace Module\Ekom\Utils\OrderReferenceProvider;


use Module\Ekom\Api\Layer\OrderLayer;

class OrderReferenceProvider implements OrderReferenceProviderInterface
{

    public static function create()
    {
        return new static();
    }

    public function getReference($type = null)
    {
        return $type . date('Ymd-His') . '-' . sprintf('%04s', (OrderLayer::countOrders() + 1));
    }


}