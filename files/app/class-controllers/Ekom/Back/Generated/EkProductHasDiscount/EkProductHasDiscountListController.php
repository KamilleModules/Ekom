<?php

namespace Controller\Ekom\Back\Generated\EkProductHasDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductHasDiscountListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_has_discount",
            'ric' => [
                "product_id",
				"discount_id",
            ],
            'label' => "product-discount",
            'labelPlural' => "product-discounts",
            'route' => "NullosAdmin_Ekom_Generated_EkProductHasDiscount_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product-discounts",
                'breadcrumb' => "ek_product_has_discount",
                'form' => "ek_product_has_discount",
                'list' => "ek_product_has_discount",
                'ric' => [
                    "product_id",
					"discount_id",
                ],

                "newItemBtnText" => "Add a new product-discount",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductHasDiscount_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductHasDiscount_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductHasDiscount_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
