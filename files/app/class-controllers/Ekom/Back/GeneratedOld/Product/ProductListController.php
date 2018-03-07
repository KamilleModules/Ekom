<?php

namespace Controller\Ekom\Back\Generated\Product;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Product_List";


        return $this->doRenderFormList([
            'title' => "Products",
            'breadcrumb' => "product",
            'form' => "product",
            'list' => "product",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}