<?php

namespace Controller\Ekom\Back\Generated\EkCategoryHasDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCategoryHasDiscountListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "discount_id", $_GET)) {        
            return $this->renderWithParent("ek_discount", [
                "discount_id" => $_GET["discount_id"],
            ], [
                "discount_id" => "id",
            ], [
                "discount",
                "discounts",
            ], "NullosAdmin_Ekom_Generated_EkDiscount_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_category_has_discount",
            'ric' => [
                "category_id",
				"discount_id",
            ],
            'label' => "category-discount",
            'labelPlural' => "category-discounts",
            'route' => "NullosAdmin_Ekom_Generated_EkCategoryHasDiscount_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "category-discounts",
                'breadcrumb' => "ek_category_has_discount",
                'form' => "ek_category_has_discount",
                'list' => "ek_category_has_discount",
                'ric' => [
                    "category_id",
					"discount_id",
                ],

                "newItemBtnText" => "Add a new category-discount",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCategoryHasDiscount_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCategoryHasDiscount_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCategoryHasDiscount_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
