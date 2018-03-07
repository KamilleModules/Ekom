<?php

namespace Controller\Ekom\Back\Generated\EkProductHasProductAttribute;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductHasProductAttributeListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "product_attribute_id", $_GET)) {        
            return $this->renderWithParent("ek_product_attribute", [
                "product_attribute_id" => $_GET["product_attribute_id"],
            ], [
                "product_attribute_id" => "id",
            ], [
                "product attribute",
                "product attributes",
            ], "NullosAdmin_Ekom_Generated_EkProductAttribute_List");
		} elseif ( array_key_exists ( "product_attribute_value_id", $_GET)) {        
            return $this->renderWithParent("ek_product_attribute_value", [
                "product_attribute_value_id" => $_GET["product_attribute_value_id"],
            ], [
                "product_attribute_value_id" => "id",
            ], [
                "product attribute value",
                "product attribute values",
            ], "NullosAdmin_Ekom_Generated_EkProductAttributeValue_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_product_has_product_attribute",
            'ric' => [
                "product_id",
				"product_attribute_id",
				"product_attribute_value_id",
            ],
            'label' => "product-product attribute",
            'labelPlural' => "product-product attributes",
            'route' => "NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product-product attributes",
                'breadcrumb' => "ek_product_has_product_attribute",
                'form' => "ek_product_has_product_attribute",
                'list' => "ek_product_has_product_attribute",
                'ric' => [
                    "product_id",
					"product_attribute_id",
					"product_attribute_value_id",
                ],

                "newItemBtnText" => "Add a new product-product attribute",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
