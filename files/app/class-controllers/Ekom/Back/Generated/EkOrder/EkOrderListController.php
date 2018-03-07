<?php

namespace Controller\Ekom\Back\Generated\EkOrder;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkOrderListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "shop_id", $_GET)) {        
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");
		} elseif ( array_key_exists ( "user_id", $_GET)) {        
            return $this->renderWithParent("ek_user", [
                "user_id" => $_GET["user_id"],
            ], [
                "user_id" => "id",
            ], [
                "user",
                "users",
            ], "NullosAdmin_Ekom_Generated_EkUser_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_order",
            'ric' => [
                "id",
            ],
            'label' => "order",
            'labelPlural' => "orders",
            'route' => "NullosAdmin_Ekom_Generated_EkOrder_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "orders",
                'breadcrumb' => "ek_order",
                'form' => "ek_order",
                'list' => "ek_order",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new order",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkOrder_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkOrder_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkOrder_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
