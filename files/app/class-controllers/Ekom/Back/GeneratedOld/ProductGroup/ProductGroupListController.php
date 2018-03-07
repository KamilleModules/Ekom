<?php

namespace Controller\Ekom\Back\Generated\ProductGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductGroup_List";


        return $this->doRenderFormList([
            'title' => "Product groups for this shop",
            'breadcrumb' => "product_group",
            'form' => "product_group",
            'list' => "product_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}