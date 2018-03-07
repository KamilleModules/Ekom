<?php

namespace Controller\Ekom\Back\Generated\OrderHasOrderStatus;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class OrderHasOrderStatusListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_OrderHasOrderStatus_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$order_id = $this->getContextFromUrl('order_id');
		$table = "ek_order_has_order_status";
		$context = [
			"order_id" => $order_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_order");
            $avatar = QuickPdo::fetch("
select $repr from `ek_order` where id=:order_id 
            ", [
				"order_id" => $order_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Order statuses for order \"$avatar\"",
            'breadcrumb' => "order_has_order_status",
            'form' => "order_has_order_status",
            'list' => "order_has_order_status",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new order status for order \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_OrderHasOrderStatus_List") . "?form&order_id=$order_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkOrder_List",             
            "buttons" => [
                [
                    "label" => "Back to order \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_Order_List") . "?id=$order_id",
                ],
            ],
            "context" => [
            	"order_id" => $order_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}