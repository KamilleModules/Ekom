<?php

namespace Controller\Ekom\Back\Generated\EkProductGroupHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductGroupHasProductListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "product_group_id", $_GET)) {        
            return $this->renderWithParent("ek_product_group", [
                "product_group_id" => $_GET["product_group_id"],
            ], [
                "product_group_id" => "id",
            ], [
                "product group",
                "product groups",
            ], "NullosAdmin_Ekom_Generated_EkProductGroup_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_product_group_has_product",
            'ric' => [
                "product_group_id",
				"product_id",
            ],
            'label' => "product group-product",
            'labelPlural' => "product group-products",
            'route' => "NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product group-products",
                'breadcrumb' => "ek_product_group_has_product",
                'form' => "ek_product_group_has_product",
                'list' => "ek_product_group_has_product",
                'ric' => [
                    "product_group_id",
					"product_id",
                ],

                "newItemBtnText" => "Add a new product group-product",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
