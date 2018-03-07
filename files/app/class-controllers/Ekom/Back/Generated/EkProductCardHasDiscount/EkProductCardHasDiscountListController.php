<?php

namespace Controller\Ekom\Back\Generated\EkProductCardHasDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductCardHasDiscountListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "discount_id", $_GET)) {        
            return $this->renderWithParent("ek_discount", [
                "discount_id" => $_GET["discount_id"],
            ], [
                "discount_id" => "id",
            ], [
                "discount",
                "discounts",
            ], "NullosAdmin_Ekom_Generated_EkDiscount_List");
		} elseif ( array_key_exists ( "product_card_id", $_GET)) {        
            return $this->renderWithParent("ek_product_card", [
                "product_card_id" => $_GET["product_card_id"],
            ], [
                "product_card_id" => "id",
            ], [
                "product card",
                "product cards",
            ], "NullosAdmin_Ekom_Generated_EkProductCard_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_product_card_has_discount",
            'ric' => [
                "product_card_id",
				"discount_id",
            ],
            'label' => "product card-discount",
            'labelPlural' => "product card-discounts",
            'route' => "NullosAdmin_Ekom_Generated_EkProductCardHasDiscount_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product card-discounts",
                'breadcrumb' => "ek_product_card_has_discount",
                'form' => "ek_product_card_has_discount",
                'list' => "ek_product_card_has_discount",
                'ric' => [
                    "product_card_id",
					"discount_id",
                ],

                "newItemBtnText" => "Add a new product card-discount",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductCardHasDiscount_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductCardHasDiscount_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductCardHasDiscount_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
