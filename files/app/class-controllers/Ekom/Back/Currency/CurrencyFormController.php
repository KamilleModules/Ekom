<?php


namespace Controller\Ekom\Back\Currency;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class CurrencyFormController extends EkomBackController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();

        $config = A::getMorphicFormConfig('Ekom', 'currency');

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
                        'currency',
                    ]),
                    "title" => "Currency",
                    "buttons" => [
                        [
                            "label" => "List of currencies",
                            "icon" => "fa fa-list",
                            "link" => E::link("NullosAdmin_Ekom_Currency_List"),
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