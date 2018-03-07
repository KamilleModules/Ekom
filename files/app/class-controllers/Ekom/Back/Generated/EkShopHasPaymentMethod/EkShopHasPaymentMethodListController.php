<?php

namespace Controller\Ekom\Back\Generated\EkShopHasPaymentMethod;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasPaymentMethodListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "payment_method_id", $_GET)) {        
            return $this->renderWithParent("ek_payment_method", [
                "payment_method_id" => $_GET["payment_method_id"],
            ], [
                "payment_method_id" => "id",
            ], [
                "payment method",
                "payment methods",
            ], "NullosAdmin_Ekom_Generated_EkPaymentMethod_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_shop_has_payment_method",
            'ric' => [
                "shop_id",
				"payment_method_id",
            ],
            'label' => "shop-payment method",
            'labelPlural' => "shop-payment methods",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasPaymentMethod_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-payment methods",
                'breadcrumb' => "ek_shop_has_payment_method",
                'form' => "ek_shop_has_payment_method",
                'list' => "ek_shop_has_payment_method",
                'ric' => [
                    "shop_id",
					"payment_method_id",
                ],

                "newItemBtnText" => "Add a new shop-payment method",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasPaymentMethod_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasPaymentMethod_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasPaymentMethod_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
