<?php

namespace Controller\Ekom\Back\Generated\ProductPurchaseStat;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductPurchaseStatListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductPurchaseStat_List";


        return $this->doRenderFormList([
            'title' => "Product purchase stats for this shop",
            'breadcrumb' => "product_purchase_stat",
            'form' => "product_purchase_stat",
            'list' => "product_purchase_stat",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product purchase stat",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}