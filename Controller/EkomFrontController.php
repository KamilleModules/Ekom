<?php


namespace Controller\Ekom;


use Bat\UriTool;
use Core\Controller\ApplicationController;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;


class EkomFrontController extends ApplicationController
{


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
        $config->replace(([
            "widgets" => [
                "topActionBar.miniCart" => [
                    "tpl" => "Ekom/MiniCart/sliding",
                    "conf" => EkomApi::inst()->cartLayer()->getCartInfo(),
                ],
            ],
        ]));
        return parent::renderByViewId($viewId, $config, $options);
    }


    protected function requiresConnectedUser()
    {
        $_SESSION['ekom.referer'] = UriTool::uri(null, [], true, true);
        $link = E::link("Ekom_login", [], true);
        return RedirectResponse::create($link);
    }
}