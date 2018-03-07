<?php

namespace Controller\Ekom\Back\Generated\EkCartDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCartDiscountListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCartDiscount_List";


        return $this->doRenderFormList([
            'title' => "Cart discounts for this shop",
            'breadcrumb' => "ek_cart_discount",
            'form' => "ek_cart_discount",
            'list' => "ek_cart_discount",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Cart discount",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}