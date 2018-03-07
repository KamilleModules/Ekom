<?php

namespace Controller\Ekom\Back\Generated\Order;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class OrderListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Order_List";


        return $this->doRenderFormList([
            'title' => "Orders for this shop",
            'breadcrumb' => "order",
            'form' => "order",
            'list' => "order",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Order",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}