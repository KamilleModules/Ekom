<?php

namespace Controller\Ekom\Back\Generated\ProductCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductCardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductCard_List";


        return $this->doRenderFormList([
            'title' => "Product cards",
            'breadcrumb' => "product_card",
            'form' => "product_card",
            'list' => "product_card",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product card",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}