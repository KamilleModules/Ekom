<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\Front\CustomerController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Model\Front\Customer\CustomerInvoicesModel;
use Module\Ekom\Utils\E;


class InvoicesController extends CustomerController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        $this->requiresClawsConnectedUser();


        $pool = $_GET;
        $userId = E::getUserId();
        $model = CustomerInvoicesModel::getModel($pool, $userId);


        $this->getClaws()->setLayout("sandwich_2c/account")
            ->setWidget("maincontent.invoices", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/Invoices/default")
                ->setConf($model)
            );
    }
}




