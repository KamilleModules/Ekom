<?php


namespace Controller\Ekom\Back\Tax;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class TaxFormController extends EkomBackController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();

        $config = A::getMorphicFormConfig('Ekom', 'tax');

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
                        'tax',
                    ]),
                    "title" => "Tax",
                    "buttons" => [
                        [
                            "label" => "List of taxes",
                            "icon" => "fa fa-list",
                            "link" => E::link("NullosAdmin_Ekom_Tax_List"),
                        ],
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