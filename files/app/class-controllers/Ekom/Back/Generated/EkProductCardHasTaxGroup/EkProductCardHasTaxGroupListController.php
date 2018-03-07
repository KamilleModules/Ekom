<?php

namespace Controller\Ekom\Back\Generated\EkProductCardHasTaxGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductCardHasTaxGroupListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "product_card_id", $_GET)) {        
            return $this->renderWithParent("ek_product_card", [
                "product_card_id" => $_GET["product_card_id"],
            ], [
                "product_card_id" => "id",
            ], [
                "product card",
                "product cards",
            ], "NullosAdmin_Ekom_Generated_EkProductCard_List");
		} elseif ( array_key_exists ( "tax_group_id", $_GET)) {        
            return $this->renderWithParent("ek_tax_group", [
                "tax_group_id" => $_GET["tax_group_id"],
            ], [
                "tax_group_id" => "id",
            ], [
                "tax group",
                "tax groups",
            ], "NullosAdmin_Ekom_Generated_EkTaxGroup_List");
		} elseif ( array_key_exists ( "shop_id", $_GET)) {        
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
            'table' => "ek_product_card_has_tax_group",
            'ric' => [
                "shop_id",
				"product_card_id",
            ],
            'label' => "product card-tax group",
            'labelPlural' => "product card-tax groups",
            'route' => "NullosAdmin_Ekom_Generated_EkProductCardHasTaxGroup_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product card-tax groups",
                'breadcrumb' => "ek_product_card_has_tax_group",
                'form' => "ek_product_card_has_tax_group",
                'list' => "ek_product_card_has_tax_group",
                'ric' => [
                    "shop_id",
					"product_card_id",
                ],

                "newItemBtnText" => "Add a new product card-tax group",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductCardHasTaxGroup_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductCardHasTaxGroup_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductCardHasTaxGroup_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
