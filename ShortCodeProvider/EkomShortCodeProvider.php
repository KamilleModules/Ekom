<?php


namespace Module\Ekom\ShortCodeProvider;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Utils\ShortCodeProvider\ShortCodeProvider;
use Module\Ekom\Api\EkomApi;

class EkomShortCodeProvider extends ShortCodeProvider
{


    public function sayHi()
    {
        a("hi");
    }

    public function getBreadCrumbs()
    {
        return EkomApi::inst()->categoryLayer()->getBreadCrumbs();
    }

    public function getProductBoxModel()
    {
        return EkomApi::inst()->productLayer()->getProductBoxModel();
    }

    public function getCartModel()
    {
        return EkomApi::inst()->cartLayer()->getCartModel();
    }

}