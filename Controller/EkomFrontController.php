<?php


namespace Controller\Ekom;


use Core\Controller\ApplicationController;
use Kamille\Utils\Laws\Config\LawsConfig;


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
        $widgets = [];
        if (null === $config) {
            $config = LawsConfig::create();
        }
        $config->replace(([
            "layout" => [
                "tpl" => "sandwich_1c/default",
            ],
            "widgets" => $widgets,
        ]));
        return parent::renderByViewId($viewId, $config, $options);
    }


}