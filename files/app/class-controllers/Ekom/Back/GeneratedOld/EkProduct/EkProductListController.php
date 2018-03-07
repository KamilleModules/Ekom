<?php

namespace Controller\Ekom\Back\Generated\EkProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProduct_List";


        return $this->doRenderFormList([
            'title' => "Products",
            'breadcrumb' => "ek_product",
            'form' => "ek_product",
            'list' => "ek_product",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}