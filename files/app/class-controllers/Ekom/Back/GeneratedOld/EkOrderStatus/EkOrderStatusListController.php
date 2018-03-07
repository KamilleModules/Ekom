<?php

namespace Controller\Ekom\Back\Generated\EkOrderStatus;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkOrderStatusListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkOrderStatus_List";


        return $this->doRenderFormList([
            'title' => "Order statuses for this shop",
            'breadcrumb' => "ek_order_status",
            'form' => "ek_order_status",
            'list' => "ek_order_status",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Order status",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}