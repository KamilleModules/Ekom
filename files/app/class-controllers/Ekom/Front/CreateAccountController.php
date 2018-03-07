<?php


namespace Controller\Ekom\Front;


use Bat\PdoTool;
use Bat\SessionTool;
use Bat\UriTool;
use Controller\Ekom\EkomFrontController;
use FormTools\Validation\OnTheFlyFormValidator;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Architecture\Response\ResponseInterface;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Module\ThisApp\Model\Front\CreateAccountModel;
use QuickPdo\QuickPdoExceptionTool;

class CreateAccountController extends EkomFrontController
{

    protected function prepareClaws()
    {
        $this->setSessionReferer = false;
        parent::prepareClaws();


        $model = CreateAccountModel::getModel();
        if ($model instanceof ResponseInterface) {
            $this->clawsReturn = $model;
            return;
        }


        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.createAccount", ClawsWidget::create()
                ->setTemplate("Ekom/CreateAccount/leaderfit")
                ->setConf($model)
            );
    }
}