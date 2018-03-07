<?php


namespace Controller\Ekom\Back\Tax;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class TaxTranslationListController extends EkomBackController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        if (
            array_key_exists('id', $_GET) ||
            array_key_exists('tax_id', $_GET)
        ) {

            $isInsert = true;
            if (array_key_exists('id', $_GET)) {
                $taxId = (int)$_GET['id'];

            } else {
                $taxId = (int)$_GET['tax_id'];
                $isInsert = false;
            }

            $context = [
                "id" => $taxId,
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
                                "label" => "Ajouter une traduction pour cette taxe",
                                "icon" => "fa fa-plus-circle",
                                "link" => E::link("NullosAdmin_Ekom_TaxTranslation_List") . "?id=$taxId",
                            ],
                            [
                                "label" => "Retour à la liste des traductions pour cette taxe",
                                "icon" => "fa fa-list",
                                "link" => E::link("NullosAdmin_Ekom_Tax_List") . "?id=$taxId",
                            ],
                        ],
                    ])
                )
                ->setWidget("maincontent.body", ClawsWidget::create()
                    ->setTemplate('Ekom/Main/FormList/default')
                    ->setConf($conf)
                );
        } else {
            throw new \Exception("Unknown tax id");
        }
    }


}