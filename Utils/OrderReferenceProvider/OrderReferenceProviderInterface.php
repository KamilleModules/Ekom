<?php


namespace Module\Ekom\Utils\OrderReferenceProvider;


interface OrderReferenceProviderInterface
{


    /**
     * @param null|string $type,
     *          the original idea was to provide references for different types of order.
     *          A default order (null), or an estimate order for instance.
     *
     * @return string, an order reference
     */
    public function getReference($type = null);
}