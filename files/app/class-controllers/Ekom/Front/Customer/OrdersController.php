<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\Front\CustomerController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Model\Front\Customer\CustomerOrderModel;
use Module\Ekom\Utils\E;

class OrdersController extends CustomerController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();


        $this->requiresClawsConnectedUser();


        $pool = $_GET;
        $userId = E::getUserId();
        $model = CustomerOrderModel::getModel($pool, $userId);


        $this->getClaws()->setLayout("sandwich_2c/account")
            ->setWidget("maincontent.orders", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/Orders/leaderfit")
                ->setConf($model)
            );
    }
}