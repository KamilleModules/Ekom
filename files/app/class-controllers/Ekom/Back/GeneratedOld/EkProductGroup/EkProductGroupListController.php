<?php

namespace Controller\Ekom\Back\Generated\EkProductGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductGroup_List";


        return $this->doRenderFormList([
            'title' => "Product groups for this shop",
            'breadcrumb' => "ek_product_group",
            'form' => "ek_product_group",
            'list' => "ek_product_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}