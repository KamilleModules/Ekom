<?php


namespace Controller\Ekom\Back\Country;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class CountryListController extends EkomBackController
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
                        'country',
                    ]),
                    "title" => "Country",
                    "buttons" => [
                        [
                            "label" => "Add a new country",
                            "icon" => "fa fa-plus-circle",
                            "link" => E::link("NullosAdmin_Ekom_Country_Form"),
                        ],
                    ],
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/List/default')
                ->setConf([
                    'listConfig' => A::getMorphicListConfig('Ekom', 'country'),
                ])
            );
    }


}