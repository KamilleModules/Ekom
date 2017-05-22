<?php


namespace Module\Ekom\ShortCodeProvider;


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
        return EkomApi::inst()->breadCrumbsLayer()->getBreadCrumbs();
    }


}