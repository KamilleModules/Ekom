<?php

namespace Controller\Ekom\Back\Generated\EccProductCardCombinationHasProductCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EccProductCardCombinationHasProductCardListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "product_card_combination_id", $_GET)) {        
            return $this->renderWithParent("ecc_product_card_combination", [
                "product_card_combination_id" => $_GET["product_card_combination_id"],
            ], [
                "product_card_combination_id" => "id",
            ], [
                "product card combination",
                "product card combinations",
            ], "NullosAdmin_Ekom_Generated_EccProductCardCombination_List");
		} elseif ( array_key_exists ( "product_card_id", $_GET)) {        
            return $this->renderWithParent("ek_product_card", [
                "product_card_id" => $_GET["product_card_id"],
            ], [
                "product_card_id" => "id",
            ], [
                "product card",
                "product cards",
            ], "NullosAdmin_Ekom_Generated_EkProductCard_List");
		} elseif ( array_key_exists ( "product_id", $_GET)) {        
            return $this->renderWithParent("ek_product", [
                "product_id" => $_GET["product_id"],
            ], [
                "product_id" => "id",
            ], [
                "product",
                "products",
            ], "NullosAdmin_Ekom_Generated_EkProduct_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ecc_product_card_combination_has_product_card",
            'ric' => [
                "id",
            ],
            'label' => "product card combination-product card",
            'labelPlural' => "product card combination-product cards",
            'route' => "NullosAdmin_Ekom_Generated_EccProductCardCombinationHasProductCard_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product card combination-product cards",
                'breadcrumb' => "ecc_product_card_combination_has_product_card",
                'form' => "ecc_product_card_combination_has_product_card",
                'list' => "ecc_product_card_combination_has_product_card",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product card combination-product card",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EccProductCardCombinationHasProductCard_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EccProductCardCombinationHasProductCard_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EccProductCardCombinationHasProductCard_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
