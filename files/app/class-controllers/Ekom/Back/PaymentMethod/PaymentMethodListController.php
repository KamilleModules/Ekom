<?php


namespace Controller\Ekom\Back\PaymentMethod;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class PaymentMethodListController extends EkomBackController
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
                        'payment_method',
                    ]),
                    "title" => "Payment method",
                    "buttons" => [
                        [
                            "label" => "Add a new payment method",
                            "icon" => "fa fa-plus-circle",
                            "link" => E::link("NullosAdmin_Ekom_PaymentMethod_Form"),
                        ],
                    ],
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/List/default')
                ->setConf([
                    'listConfig' => A::getMorphicListConfig('Ekom', 'payment_method'),
                ])
            );
    }


}