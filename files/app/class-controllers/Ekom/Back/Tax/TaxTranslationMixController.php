<?php


namespace Controller\Ekom\Back\Tax;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class TaxTranslationMixController extends EkomBackController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        if (
            array_key_exists("id", $_GET) ||
            array_key_exists("tax_id", $_GET) ||
            array_key_exists("form", $_GET)
        ) {


            $taxId = $_GET['tax_id'];
            $context = [
                "tax_id" => $taxId,
            ];
            $conf = [
                'listConfig' => A::getMorphicListConfig('Ekom', 'tax_translation', $context),
            ];

            $config = A::getMorphicFormConfig('Ekom', 'tax_translation');
            $this->handleMorphicForm($config);
            $conf['formConfig'] = $config;


            $this->getClaws()
                //--------------------------------------------
                // MAIN
                //--------------------------------------------
                ->setWidget("maincontent.pageTop", ClawsWidget::create()
                    ->setTemplate('Ekom/Main/PageTop/default')
                    ->setConf([
                        "breadcrumbs" => BreadcrumbsHelper::getBreadCrumbsModel([
                            'admin',
                            'tax_translation',
                        ]),
                        "title" => "Tax translation",
                        "buttons" => [
                            [
                                "label" => "Add a new tax translation",
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


        } else {
            throw new \Exception("No id found for the tax");
        }

    }


}