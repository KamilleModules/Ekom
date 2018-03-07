<?php


namespace Controller\Ekom\Front;


use Controller\Ekom\EkomFrontController;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Model\Front\CreateAccountSuccessModel;

class CreateAccountValidateController extends EkomFrontController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();

        if (array_key_exists("hash", $_GET)) {
            $hash = $_GET['hash'];
            $model = [];
            if (false === UserLayer::activateByHash($hash)) {
                $model = ClawsWidgetError::create()->setErrorMessage("Votre compte n'a pas pu être activé.
Si cela pose des problèmes, merci de contacter notre service commercial.                
                ")->getModel();
            }



        } else {
            $model = ClawsWidgetError::create()->setErrorMessage("Les paramètres passés ne sont pas valides")->getModel();
        }
        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.createAccountValidate", ClawsWidget::create()
                ->setTemplate("Ekom/CreateAccountValidate/leaderfit")
                ->setConf($model)
            );
    }


}