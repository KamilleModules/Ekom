<?php



namespace Module\Ekom\Utils\OrderBuilder\Collection;


use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\OrderBuilder\OrderBuilderInterface;

interface OrderBuilderCollectionInterface{



    /**
     * @return OrderBuilderInterface[], array of name => OrderBuilderInterface
     */
    public function all();



    /**
     *
     * @return OrderBuilderInterface, or mixed if throwEx is false
     * @throws EkomException if the orderBuilder was not found and throwEx is true
     */
    public function get($name, $throwEx = true, $default=null);

}