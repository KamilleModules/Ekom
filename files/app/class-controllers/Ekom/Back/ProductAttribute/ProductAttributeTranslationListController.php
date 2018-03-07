<?php


namespace Controller\Ekom\Back\ProductAttribute;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;
use Module\Ekom\Utils\E;

class ProductAttributeTranslationListController extends EkomBackController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        if (
            array_key_exists('id', $_GET) ||
            array_key_exists('product_attribute_id', $_GET)
        ) {

            if (array_key_exists('id', $_GET)) {
                $product_attributeId = (int)$_GET['id'];

            } else {
                $product_attributeId = (int)$_GET['product_attribute_id'];
            }

            $context = [
                "id" => $product_attributeId,
            ];
            $conf = [
                'listConfig' => A::getMorphicListConfig('Ekom', 'product_attribute_translation', $context),
            ];


            $config = A::getMorphicFormConfig('Ekom', 'product_attribute_translation');
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
                            'product_attribute_translation',
                        ]),
                        "title" => "Product attribute translation",
                        "buttons" => [
                            [
                                "label" => "Ajouter une traduction pour cet attribut de produit",
                                "icon" => "fa fa-plus-circle",
                                "link" => E::link("NullosAdmin_Ekom_ProductAttributeTranslation_List") . "?id=$product_attributeId",
                            ],
                            [
                                "label" => "Retour à la liste des traductions pour cet attribut de produit",
                                "icon" => "fa fa-list",
                                "link" => E::link("NullosAdmin_Ekom_ProductAttribute_List") . "?id=$product_attributeId",
                            ],
                        ],
                    ])
                )
                ->setWidget("maincontent.body", ClawsWidget::create()
                    ->setTemplate('Ekom/Main/FormList/default')
                    ->setConf($conf)
                );
        } else {
            throw new \Exception("Unknown product_attribute id");
        }
    }


}