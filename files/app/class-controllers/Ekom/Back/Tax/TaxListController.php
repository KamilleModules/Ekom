<?php


namespace Controller\Ekom\Back\Tax;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class TaxListController extends EkomBackController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        $conf = [
            'listConfig' => A::getMorphicListConfig('Ekom', 'tax'),
        ];

        if (
            array_key_exists("id", $_GET) ||
            array_key_exists("form", $_GET)
        ) {
            $config = A::getMorphicFormConfig('Ekom', 'tax');
            $this->handleMorphicForm($config);
            $conf['formConfig'] = $config;

            if (array_key_exists("id", $_GET)) {
                $listConfig = A::getMorphicListConfig('Ekom', 'tax_translation', [
                    "tax_id" => (int)$_GET['id'],
                ]);
                $conf['boundListConfig'] = $listConfig;
            }
        }


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
                            "link" => "?form",
                        ],
                    ],
                ])
            )
            ->setWidget("maincontent.body", ClawsWidget::create()
                ->setTemplate('Ekom/Main/FormList/default')
                ->setConf($conf)
            );
    }


}