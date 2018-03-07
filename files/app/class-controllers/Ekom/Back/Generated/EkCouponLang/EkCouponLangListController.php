<?php

namespace Controller\Ekom\Back\Generated\EkCouponLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCouponLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "coupon_id", $_GET)) {        
            return $this->renderWithParent("ek_coupon", [
                "coupon_id" => $_GET["coupon_id"],
            ], [
                "coupon_id" => "id",
            ], [
                "coupon",
                "coupons",
            ], "NullosAdmin_Ekom_Generated_EkCoupon_List");
		} elseif ( array_key_exists ( "lang_id", $_GET)) {        
            return $this->renderWithParent("ek_lang", [
                "lang_id" => $_GET["lang_id"],
            ], [
                "lang_id" => "id",
            ], [
                "lang",
                "langs",
            ], "NullosAdmin_Ekom_Generated_EkLang_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_coupon_lang",
            'ric' => [
                "lang_id",
				"coupon_id",
            ],
            'label' => "coupon lang",
            'labelPlural' => "coupon langs",
            'route' => "NullosAdmin_Ekom_Generated_EkCouponLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "coupon langs",
                'breadcrumb' => "ek_coupon_lang",
                'form' => "ek_coupon_lang",
                'list' => "ek_coupon_lang",
                'ric' => [
                    "lang_id",
					"coupon_id",
                ],

                "newItemBtnText" => "Add a new coupon lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCouponLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCouponLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCouponLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
