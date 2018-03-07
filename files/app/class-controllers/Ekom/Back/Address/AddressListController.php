<?php


namespace Controller\Ekom\Back\Address;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class AddressListController extends EkomBackController
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
                        'address',
                    ]),
                    "title" => "Address",
                    "buttons" => [
                        [
                            "label" => "Add a new address",
                            "icon" => "fa fa-plus-circle",
                            "link" => E::link("NullosAdmin_Ekom_Address_Form"),
                        ],
                    ],
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/List/default')
                ->setConf([
                    'listConfig' => A::getMorphicListConfig('Ekom', 'address'),
                ])
            );
    }


}