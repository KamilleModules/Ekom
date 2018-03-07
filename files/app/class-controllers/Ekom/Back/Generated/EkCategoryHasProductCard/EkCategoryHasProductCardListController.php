<?php

namespace Controller\Ekom\Back\Generated\EkCategoryHasProductCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCategoryHasProductCardListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "category_id", $_GET)) {        
            return $this->renderWithParent("ek_category", [
                "category_id" => $_GET["category_id"],
            ], [
                "category_id" => "id",
            ], [
                "category",
                "categories",
            ], "NullosAdmin_Ekom_Generated_EkCategory_List");
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
            'table' => "ek_category_has_product_card",
            'ric' => [
                "category_id",
				"product_card_id",
            ],
            'label' => "category-product card",
            'labelPlural' => "category-product cards",
            'route' => "NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "category-product cards",
                'breadcrumb' => "ek_category_has_product_card",
                'form' => "ek_category_has_product_card",
                'list' => "ek_category_has_product_card",
                'ric' => [
                    "category_id",
					"product_card_id",
                ],

                "newItemBtnText" => "Add a new category-product card",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
