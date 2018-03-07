<?php

namespace Controller\Ekom\Back\Generated\EkOrder;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkOrderListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkOrder_List";


        return $this->doRenderFormList([
            'title' => "Orders for this shop",
            'breadcrumb' => "ek_order",
            'form' => "ek_order",
            'list' => "ek_order",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Order",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}