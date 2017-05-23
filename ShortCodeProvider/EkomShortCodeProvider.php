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

    public function getProductBoxByCardId()
    {
        $cardId = ApplicationRegistry::get("ekom.cardId");
        return EkomApi::inst()->productLayer()->getProductBoxModelByCardId($cardId);

    }

}