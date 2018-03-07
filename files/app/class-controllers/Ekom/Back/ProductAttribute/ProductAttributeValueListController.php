<?php


namespace Controller\Ekom\Back\ProductAttribute;


use Controller\Ekom\Back\EkomBackController;
use Core\Services\A;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\Helper\BreadcrumbsHelper;

class ProductAttributeValueListController extends EkomBackController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        $conf = [
            'listConfig' => A::getMorphicListConfig('Ekom', 'product_attribute_value'),
        ];

        if (
            array_key_exists("id", $_GET) ||
            array_key_exists("form", $_GET)
        ) {
            $config = A::getMorphicFormConfig('Ekom', 'product_attribute_value');
            $this->handleMorphicForm($config);
            $conf['formConfig'] = $config;

            if (array_key_exists("id", $_GET)) {
                $listConfig = A::getMorphicListConfig('Ekom', 'product_attribute_value_translation', [
                    "product_attribute_value_id" => (int)$_GET['id'],
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
                        'product_attribute_value',
                    ]),
                    "title" => "Product attribute",
                    "buttons" => [
                        [
                            "label" => "Add a new product attribute value",
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