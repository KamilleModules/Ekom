<?php


namespace Module\Ekom\Utils\OrderBuilder\Collection;


use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\OrderBuilder\OrderBuilderInterface;

class OrderBuilderCollection implements OrderBuilderCollectionInterface
{


    private $builders;


    public function __construct()
    {
        $this->builders = [];
    }


    public function setBuilder($name, OrderBuilderInterface $builder)
    {
        $this->builders[$name] = $builder;
        return $this;
    }


    public function all()
    {
        return $this->builders;
    }

    public function get($name, $throwEx = true, $default = null)
    {

        if (array_key_exists($name, $this->builders)) {
            return $this->builders[$name];
        }
        if (true === $throwEx) {
            throw new EkomException("OrderBuilder with name $name not found");
        }
        return $default;
    }


}