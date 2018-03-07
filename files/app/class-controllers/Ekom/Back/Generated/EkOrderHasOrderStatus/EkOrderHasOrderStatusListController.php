<?php

namespace Controller\Ekom\Back\Generated\EkOrderHasOrderStatus;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkOrderHasOrderStatusListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "order_id", $_GET)) {        
            return $this->renderWithParent("ek_order", [
                "order_id" => $_GET["order_id"],
            ], [
                "order_id" => "id",
            ], [
                "order",
                "orders",
            ], "NullosAdmin_Ekom_Generated_EkOrder_List");
		} elseif ( array_key_exists ( "order_status_id", $_GET)) {        
            return $this->renderWithParent("ek_order_status", [
                "order_status_id" => $_GET["order_status_id"],
            ], [
                "order_status_id" => "id",
            ], [
                "order status",
                "order statuses",
            ], "NullosAdmin_Ekom_Generated_EkOrderStatus_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_order_has_order_status",
            'ric' => [
                "id",
            ],
            'label' => "order-order status",
            'labelPlural' => "order-order statuses",
            'route' => "NullosAdmin_Ekom_Generated_EkOrderHasOrderStatus_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "order-order statuses",
                'breadcrumb' => "ek_order_has_order_status",
                'form' => "ek_order_has_order_status",
                'list' => "ek_order_has_order_status",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new order-order status",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkOrderHasOrderStatus_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkOrderHasOrderStatus_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkOrderHasOrderStatus_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
