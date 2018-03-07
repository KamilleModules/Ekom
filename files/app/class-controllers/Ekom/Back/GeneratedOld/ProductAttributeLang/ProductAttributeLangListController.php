<?php

namespace Controller\Ekom\Back\Generated\ProductAttributeLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductAttributeLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductAttributeLang_List";


        return $this->doRenderFormList([
            'title' => "Product attribute langs",
            'breadcrumb' => "product_attribute_lang",
            'form' => "product_attribute_lang",
            'list' => "product_attribute_lang",
            'ric' => [
                'product_attribute_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Product attribute lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}