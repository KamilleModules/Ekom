<?php


namespace Controller\Ekom;


use Bat\SessionTool;
use Bat\UriTool;
use Core\Controller\ApplicationController;
use Core\Services\Hooks;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\E;


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


//    protected function renderByViewId($viewId, LawsConfig $config = null, array $options = [])
//    {
//        /**
//         * From here we can configure all the common/implicit widgets.
//         * Those are the widgets specific to ekom module and common to all controllers.
//         */
//        if (null === $config) {
//            $config = LawsConfig::create();
//        }
//
//        $commonWidgets = [];
//        Hooks::call("Ekom_FrontController_decorateCommonWidgets", $commonWidgets);
//        $config->replace(([
//            "widgets" => $commonWidgets,
//        ]));
//
//
//        if (true === $this->setSessionReferer) {
//            EkomSession::set("referer", UriTool::uri(null, [], true, true));
//        }
//
//
////        $configInfo = [
////            $viewId,
////            $config,
////        ];
////        Hooks::call("Ekom_decorate_LawsConfig", $configInfo);
////        $conf = $configInfo[1];
//
//
//        return parent::renderByViewId($viewId, $config, $options);
//    }


    protected function requiresConnectedUser()
    {
        EkomSession::set("referer", UriTool::uri(null, [], true, true));
        $link = E::link("Ekom_login", [], true);
        return RedirectResponse::create($link);
    }

    protected function prepareClaws() // override me
    {
        if (true === $this->setSessionReferer) {
            EkomSession::set("referer", UriTool::uri(null, [], true, true));
        }
        parent::prepareClaws();
    }


}