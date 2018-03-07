<?php

namespace Controller\Ekom\Back\Generated\EkCouponHasCartDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCouponHasCartDiscountListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "cart_discount_id", $_GET)) {        
            return $this->renderWithParent("ek_cart_discount", [
                "cart_discount_id" => $_GET["cart_discount_id"],
            ], [
                "cart_discount_id" => "id",
            ], [
                "cart discount",
                "cart discounts",
            ], "NullosAdmin_Ekom_Generated_EkCartDiscount_List");
		} elseif ( array_key_exists ( "coupon_id", $_GET)) {        
            return $this->renderWithParent("ek_coupon", [
                "coupon_id" => $_GET["coupon_id"],
            ], [
                "coupon_id" => "id",
            ], [
                "coupon",
                "coupons",
            ], "NullosAdmin_Ekom_Generated_EkCoupon_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_coupon_has_cart_discount",
            'ric' => [
                "coupon_id",
				"cart_discount_id",
            ],
            'label' => "coupon-cart discount",
            'labelPlural' => "coupon-cart discounts",
            'route' => "NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "coupon-cart discounts",
                'breadcrumb' => "ek_coupon_has_cart_discount",
                'form' => "ek_coupon_has_cart_discount",
                'list' => "ek_coupon_has_cart_discount",
                'ric' => [
                    "coupon_id",
					"cart_discount_id",
                ],

                "newItemBtnText" => "Add a new coupon-cart discount",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
