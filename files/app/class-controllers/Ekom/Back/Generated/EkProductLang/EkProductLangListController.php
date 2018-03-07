<?php

namespace Controller\Ekom\Back\Generated\EkProductLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductLangListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_lang",
            'ric' => [
                "product_id",
				"lang_id",
            ],
            'label' => "product lang",
            'labelPlural' => "product langs",
            'route' => "NullosAdmin_Ekom_Generated_EkProductLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product langs",
                'breadcrumb' => "ek_product_lang",
                'form' => "ek_product_lang",
                'list' => "ek_product_lang",
                'ric' => [
                    "product_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new product lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
