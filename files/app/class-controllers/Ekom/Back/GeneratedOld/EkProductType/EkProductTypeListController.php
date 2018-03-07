<?php

namespace Controller\Ekom\Back\Generated\EkProductType;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductTypeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductType_List";


        return $this->doRenderFormList([
            'title' => "Product types for this shop",
            'breadcrumb' => "ek_product_type",
            'form' => "ek_product_type",
            'list' => "ek_product_type",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product type",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}