<?php


namespace Controller\Ekom;


use Bat\SessionTool;
use Bat\UriTool;
use Core\Controller\ApplicationController;
use Core\Services\Hooks;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Utils\Ball\Ball;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\E;
use Module\EkomEstimate\Api\EkomEstimateApi;


class EkomFrontController extends ApplicationController
{

    protected $setSessionReferer = true;

//    protected function renderPageError($msg, $title = null)
//    {
//        XLog::error(get_called_class() . ": You need to define the prc first");
//        if (null === $title) {
//            $title = "Oops";
//        }
//        return $this->renderByViewId("NullosAdmin/errorPage", [
//            'widgets' => [
//                'maincontent.error' => [
//                    "conf" => [
//                        'message' => $msg,
//                        'title' => $title,
//                    ],
//                ],
//            ],
//        ]);
//    }


    protected function renderByViewId($viewId, LawsConfig $config = null, array $options = [])
    {
        /**
         * From here we can configure all the common/implicit widgets.
         * Those are the widgets specific to ekom module and common to all controllers.
         */
        if (null === $config) {
            $config = LawsConfig::create();
        }

        $ball = Ball::create()
            ->push("viewId", $viewId)
            ->push('commonWidgets', []);
        Hooks::call("Ekom_FrontController_decorateCommonWidgets", $ball);
        $config->replace(([
            "widgets" => $ball->pull('commonWidgets'),
        ]));


        if (true === $this->setSessionReferer) {
            EkomSession::set('referer', UriTool::uri(null, [], true, true));
        }


//        $configInfo = [
//            $viewId,
//            $config,
//        ];
//        Hooks::call("Ekom_decorate_LawsConfig", $configInfo);
//        $conf = $configInfo[1];


        return parent::renderByViewId($viewId, $config, $options);
    }


    protected function requiresConnectedUser()
    {
        EkomSession::set('referer', UriTool::uri(null, [], true, true));
        $link = E::link("Ekom_login", [], true);
        return RedirectResponse::create($link);
    }


    /**
     * Call this from your protected prepareClaws method if
     * you need the user to be connected (and if she's not she will
     * be automatically redirected to the login page)
     */
    protected function requiresClawsConnectedUser()
    {
        if (false === EkomApi::inst()->connexionLayer()->isConnected()) {
            EkomSession::set('referer', UriTool::uri(null, [], true, true));
            $link = E::link("Ekom_login", [], true);
            $this->clawsReturn = RedirectResponse::create($link);
        }
    }

    protected function prepareClaws() // override me
    {
        if (true === $this->setSessionReferer) {
            EkomSession::set('referer', UriTool::uri(null, [], true, true));
        }

        parent::prepareClaws();


        //--------------------------------------------
        // MODULES ADD ELEMENTS COMMON TO ALL EKOM FRONT PAGES
        //--------------------------------------------
        $claws = $this->getClaws();
        Hooks::call("Ekom_feedFrontControllerClaws", $claws);
    }

}