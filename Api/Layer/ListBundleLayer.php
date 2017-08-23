<?php


namespace Module\Ekom\Api\Layer;



use Core\Services\X;
use ListParams\ListBundleFactory\ListBundleFactoryInterface;

class ListBundleLayer
{

    public function getUserAccountInvoicesListBundle(){
        /**
         * @var $factory ListBundleFactoryInterface
         */
        $factory = X::get("Ekom_ListBundleFactory");
        return $factory->getListBundle("customer.account.orders");
    }

    public function getUserAccountOrdersListBundle(){
        /**
         * @var $factory ListBundleFactoryInterface
         */
        $factory = X::get("Ekom_ListBundleFactory");
        return $factory->getListBundle("customer.account.order-history");
    }
}