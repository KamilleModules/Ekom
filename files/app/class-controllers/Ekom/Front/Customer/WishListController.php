<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\Front\CustomerController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Model\Front\Customer\CustomerWishListModel;
use Module\Ekom\Utils\E;

class WishListController extends CustomerController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();
        $this->requiresClawsConnectedUser();


        $pool = $_GET;
        $userId = E::getUserId();
        $model = CustomerWishListModel::getModel($pool, $userId);


        $this->getClaws()->setLayout("sandwich_2c/account")
            ->setWidget("maincontent.invoices", ClawsWidget::create()
//                ->setTemplate("Ekom/Customer/Invoices/default")
                ->setTemplate("Ekom/Customer/WishList/hybrid")
                ->setConf($model)
            );
    }
}





