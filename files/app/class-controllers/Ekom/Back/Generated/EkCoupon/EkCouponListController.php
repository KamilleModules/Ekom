<?php

namespace Controller\Ekom\Back\Generated\EkCoupon;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCouponListController extends EkomBackSimpleFormListController
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
            'table' => "ek_coupon",
            'ric' => [
                "id",
            ],
            'label' => "coupon",
            'labelPlural' => "coupons",
            'route' => "NullosAdmin_Ekom_Generated_EkCoupon_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "coupons",
                'breadcrumb' => "ek_coupon",
                'form' => "ek_coupon",
                'list' => "ek_coupon",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new coupon",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCoupon_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCoupon_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCoupon_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
