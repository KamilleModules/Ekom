<?php

namespace Controller\Ekom\Back\Generated\EkProductAttributeLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductAttributeLangListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "product_attribute_id", $_GET)) {        
            return $this->renderWithParent("ek_product_attribute", [
                "product_attribute_id" => $_GET["product_attribute_id"],
            ], [
                "product_attribute_id" => "id",
            ], [
                "product attribute",
                "product attributes",
            ], "NullosAdmin_Ekom_Generated_EkProductAttribute_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_product_attribute_lang",
            'ric' => [
                "product_attribute_id",
				"lang_id",
            ],
            'label' => "product attribute lang",
            'labelPlural' => "product attribute langs",
            'route' => "NullosAdmin_Ekom_Generated_EkProductAttributeLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product attribute langs",
                'breadcrumb' => "ek_product_attribute_lang",
                'form' => "ek_product_attribute_lang",
                'list' => "ek_product_attribute_lang",
                'ric' => [
                    "product_attribute_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new product attribute lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductAttributeLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductAttributeLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductAttributeLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
