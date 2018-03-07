<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Model\Front\CreateAccountSuccessModel;

class CreateAccountSuccessController extends EkomFrontController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();
        $model = CreateAccountSuccessModel::create()->getModel($_GET);
        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.createAccountSuccess", ClawsWidget::create()
                ->setTemplate("Ekom/CreateAccountSuccess/leaderfit")
                ->setConf($model)
            );
    }


}