<?php

namespace Controller\Ekom\Back\Generated\OrderStatus;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class OrderStatusListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_OrderStatus_List";


        return $this->doRenderFormList([
            'title' => "Order statuses for this shop",
            'breadcrumb' => "order_status",
            'form' => "order_status",
            'list' => "order_status",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Order status",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}