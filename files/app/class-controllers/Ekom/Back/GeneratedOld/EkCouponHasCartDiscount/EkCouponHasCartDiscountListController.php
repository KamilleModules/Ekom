<?php

namespace Controller\Ekom\Back\Generated\EkCouponHasCartDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCouponHasCartDiscountListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$coupon_id = $this->getContextFromUrl('coupon_id');
		$table = "ek_coupon_has_cart_discount";
		$context = [
			"coupon_id" => $coupon_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_coupon");
            $avatar = QuickPdo::fetch("
select $repr from `ek_coupon` where id=:coupon_id 
            ", [
				"coupon_id" => $coupon_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Cart discounts for coupon \"$avatar\"",
            'breadcrumb' => "ek_coupon_has_cart_discount",
            'form' => "ek_coupon_has_cart_discount",
            'list' => "ek_coupon_has_cart_discount",
            'ric' => [
                'coupon_id',
                'cart_discount_id',
            ],
            
            "newItemBtnText" => "Add a new cart discount for coupon \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List") . "?form&coupon_id=$coupon_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCoupon_List",             
            "buttons" => [
                [
                    "label" => "Back to coupon \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCoupon_List") . "?id=$coupon_id",
                ],
            ],
            "context" => [
            	"coupon_id" => $coupon_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}