<?php

namespace Controller\Ekom\Back\Generated\EkProductAttributeValueLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductAttributeValueLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "lang_id", $_GET)) {        
            return $this->renderWithParent("ek_lang", [
                "lang_id" => $_GET["lang_id"],
            ], [
                "lang_id" => "id",
            ], [
                "lang",
                "langs",
            ], "NullosAdmin_Ekom_Generated_EkLang_List");
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
            'table' => "ek_product_attribute_value_lang",
            'ric' => [
                "product_attribute_value_id",
				"lang_id",
            ],
            'label' => "product attribute value lang",
            'labelPlural' => "product attribute value langs",
            'route' => "NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product attribute value langs",
                'breadcrumb' => "ek_product_attribute_value_lang",
                'form' => "ek_product_attribute_value_lang",
                'list' => "ek_product_attribute_value_lang",
                'ric' => [
                    "product_attribute_value_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new product attribute value lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
