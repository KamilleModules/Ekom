<?php

namespace Controller\Ekom\Back\Generated\ProductAttributeValue;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductAttributeValueListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductAttributeValue_List";


        return $this->doRenderFormList([
            'title' => "Product attribute values",
            'breadcrumb' => "product_attribute_value",
            'form' => "product_attribute_value",
            'list' => "product_attribute_value",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product attribute value",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}