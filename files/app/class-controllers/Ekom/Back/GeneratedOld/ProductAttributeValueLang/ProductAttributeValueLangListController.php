<?php

namespace Controller\Ekom\Back\Generated\ProductAttributeValueLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductAttributeValueLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductAttributeValueLang_List";


        return $this->doRenderFormList([
            'title' => "Product attribute value langs",
            'breadcrumb' => "product_attribute_value_lang",
            'form' => "product_attribute_value_lang",
            'list' => "product_attribute_value_lang",
            'ric' => [
                'product_attribute_value_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Product attribute value lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}