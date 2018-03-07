<?php

namespace Controller\Ekom\Back\Generated\EkCartDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCartDiscountListController extends EkomBackSimpleFormListController
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
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_cart_discount",
            'ric' => [
                "id",
            ],
            'label' => "cart discount",
            'labelPlural' => "cart discounts",
            'route' => "NullosAdmin_Ekom_Generated_EkCartDiscount_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "cart discounts",
                'breadcrumb' => "ek_cart_discount",
                'form' => "ek_cart_discount",
                'list' => "ek_cart_discount",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new cart discount",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCartDiscount_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCartDiscount_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCartDiscount_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
