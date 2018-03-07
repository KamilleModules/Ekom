<?php


namespace Controller\Ekom\Back\Address;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class AddressFormController extends EkomBackController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();

        $config = A::getMorphicFormConfig('Ekom', 'address');

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
                        'address',
                    ]),
                    "title" => "Address",
                    "buttons" => [
                        [
                            "label" => "List of addresses",
                            "icon" => "fa fa-list",
                            "link" => E::link("NullosAdmin_Ekom_Address_List"),
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