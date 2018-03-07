<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Model\Front\LoginFormModel\SokoLoginFormModel;

class LoginController extends EkomFrontController
{

    protected function prepareClaws()
    {
        $this->setSessionReferer = false;
        parent::prepareClaws();


        $response = null;
        $model = SokoLoginFormModel::getFormModel($response);
        if (null !== $response) {
            $this->clawsReturn = $response;
            return;
        }

        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.login", ClawsWidget::create()
                ->setTemplate("Ekom/Login/leaderfit")
                ->setConf($model)
            );
    }









    //--------------------------------------------
    //
    //--------------------------------------------
    private function prepareClaws_2017_11_23()
    {
        $this->setSessionReferer = false;
        parent::prepareClaws();


        $response = null;
        $model = EkomApi::inst()->connexionLayer()->handleLoginForm($response);
        if (null !== $response) {
            $this->clawsReturn = $response;
            return;
        }

        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.login", ClawsWidget::create()
                ->setTemplate("Ekom/Login/leaderfit")
                ->setConf($model)
            );
    }
}