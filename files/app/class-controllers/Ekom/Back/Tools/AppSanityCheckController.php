<?php


namespace Controller\Ekom\Back\Tools;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Architecture\Controller\Exception\ClawsHttpResponseException;
use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;

class AppSanityCheckController extends EkomBackController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();

        $config = A::getMorphicFormConfig('Ekom', 'tools/app_sanitycheck');

        $this->handleMorphicForm($config);


        $this->getClaws()
            //--------------------------------------------
            // MAIN
            //--------------------------------------------
            ->setWidget("maincontent.pageTop", ClawsWidget::create()
                ->setTemplate('Ekom/Main/PageTop/default')
                ->setConf([
                    "breadcrumbs" => BreadcrumbsHelper::getBreadCrumbsModel([
                        'admin',
                        'app_sanitycheck',
                    ]),
                    "title" => "App Sanity check",
                    "buttons" => [
//                        [
//                            "label" => "List of currencies",
//                            "icon" => "fa fa-list",
//                            "link" => E::link("NullosAdmin_Ekom_Currency_List"),
//                        ],
                    ],
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/Form/default')
                ->setConf([
                    'formConfig' => $config,
                ])
            );
    }
}