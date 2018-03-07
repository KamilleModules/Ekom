<?php

namespace Controller\Ekom\Back\Generated\EkProductCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductCardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductCard_List";


        return $this->doRenderFormList([
            'title' => "Product cards",
            'breadcrumb' => "ek_product_card",
            'form' => "ek_product_card",
            'list' => "ek_product_card",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product card",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}