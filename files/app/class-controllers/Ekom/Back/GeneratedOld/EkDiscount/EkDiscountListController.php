<?php

namespace Controller\Ekom\Back\Generated\EkDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkDiscountListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkDiscount_List";


        return $this->doRenderFormList([
            'title' => "Discounts for this shop",
            'breadcrumb' => "ek_discount",
            'form' => "ek_discount",
            'list' => "ek_discount",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Discount",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}