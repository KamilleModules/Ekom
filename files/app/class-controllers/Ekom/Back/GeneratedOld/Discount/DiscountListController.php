<?php

namespace Controller\Ekom\Back\Generated\Discount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiscountListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Discount_List";


        return $this->doRenderFormList([
            'title' => "Discounts for this shop",
            'breadcrumb' => "discount",
            'form' => "discount",
            'list' => "discount",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Discount",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}