<?php

namespace Controller\Ekom\Back\Generated\ProductAttribute;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductAttributeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductAttribute_List";


        return $this->doRenderFormList([
            'title' => "Product attributes",
            'breadcrumb' => "product_attribute",
            'form' => "product_attribute",
            'list' => "product_attribute",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product attribute",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}