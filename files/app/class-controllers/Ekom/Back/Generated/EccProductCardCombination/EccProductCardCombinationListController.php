<?php

namespace Controller\Ekom\Back\Generated\EccProductCardCombination;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EccProductCardCombinationListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "product_id", $_GET)) {        
            return $this->renderWithParent("ek_product", [
                "product_id" => $_GET["product_id"],
            ], [
                "product_id" => "id",
            ], [
                "product",
                "products",
            ], "NullosAdmin_Ekom_Generated_EkProduct_List");
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
            'table' => "ecc_product_card_combination",
            'ric' => [
                "id",
            ],
            'label' => "product card combination",
            'labelPlural' => "product card combinations",
            'route' => "NullosAdmin_Ekom_Generated_EccProductCardCombination_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product card combinations",
                'breadcrumb' => "ecc_product_card_combination",
                'form' => "ecc_product_card_combination",
                'list' => "ecc_product_card_combination",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product card combination",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EccProductCardCombination_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EccProductCardCombination_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EccProductCardCombination_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
