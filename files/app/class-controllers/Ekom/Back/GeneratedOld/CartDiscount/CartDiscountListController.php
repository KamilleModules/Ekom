<?php

namespace Controller\Ekom\Back\Generated\CartDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CartDiscountListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_CartDiscount_List";


        return $this->doRenderFormList([
            'title' => "Cart discounts for this shop",
            'breadcrumb' => "cart_discount",
            'form' => "cart_discount",
            'list' => "cart_discount",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Cart discount",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}