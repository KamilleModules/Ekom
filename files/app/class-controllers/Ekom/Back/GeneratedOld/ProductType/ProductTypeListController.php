<?php

namespace Controller\Ekom\Back\Generated\ProductType;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductTypeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductType_List";


        return $this->doRenderFormList([
            'title' => "Product types for this shop",
            'breadcrumb' => "product_type",
            'form' => "product_type",
            'list' => "product_type",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product type",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}