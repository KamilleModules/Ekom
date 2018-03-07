<?php

namespace Controller\Ekom\Back\Generated\EkProductAttributeValueLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductAttributeValueLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List";


        return $this->doRenderFormList([
            'title' => "Product attribute value langs",
            'breadcrumb' => "ek_product_attribute_value_lang",
            'form' => "ek_product_attribute_value_lang",
            'list' => "ek_product_attribute_value_lang",
            'ric' => [
                'product_attribute_value_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Product attribute value lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}