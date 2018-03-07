<?php


namespace Controller\Ekom\Back\Carrier;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class CarrierFormController extends EkomBackController
{

    protected function prepareClaws()
    {
        parent::prepareClaws();

        $config = A::getMorphicFormConfig('Ekom', 'carrier');

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
                        'carrier',
                    ]),
                    "title" => "Carrier",
                    "buttons" => [
                        [
                            "label" => "List of carriers",
                            "icon" => "fa fa-list",
                            "link" => E::link("NullosAdmin_Ekom_Carrier_List"),
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