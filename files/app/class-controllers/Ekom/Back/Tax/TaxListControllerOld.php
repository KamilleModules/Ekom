<?php


namespace Controller\Ekom\Back\Tax;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class TaxListControllerOld extends EkomBackController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();

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
                            "label" => "Add a new tax",
                            "icon" => "fa fa-plus-circle",
                            "link" => E::link("NullosAdmin_Ekom_Tax_Form"),
                        ],
                    ],
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/List/default')
                ->setConf([
                    'listConfig' => A::getMorphicListConfig('Ekom', 'tax'),
                ])
            );
    }


}