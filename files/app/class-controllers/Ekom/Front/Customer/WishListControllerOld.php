<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\Front\CustomerController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;

class WishListControllerOld extends CustomerController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();
        $this->requiresClawsConnectedUser();


        $list = EkomApi::inst()->listBundleLayer()->getWishListItemsListBundle();


        $this->getClaws()
            ->setWidget("maincontent.wishList", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/WishList/default")
                ->setConf([
                    'listBundle' => $list,
                ])
            );
    }
}





