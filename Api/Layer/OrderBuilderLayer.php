<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\X;
use Module\Ekom\Utils\OrderBuilder\Collection\OrderBuilderCollectionInterface;

class OrderBuilderLayer
{

    public function get($name, $throwEx = true, $default = null)
    {
        /**
         * @var $col OrderBuilderCollectionInterface
         */
        $col = X::get("Ekom_OrderBuilderCollection");
        return $col->get($name, $throwEx, $default);
    }
}
