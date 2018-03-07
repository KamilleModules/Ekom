<?php

namespace Controller\Ekom\Back\Generated\EkProductPurchaseStat;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductPurchaseStatListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductPurchaseStat_List";


        return $this->doRenderFormList([
            'title' => "Product purchase stats for this shop",
            'breadcrumb' => "ek_product_purchase_stat",
            'form' => "ek_product_purchase_stat",
            'list' => "ek_product_purchase_stat",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product purchase stat",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}