<?php

namespace Controller\Ekom\Back\Generated\EkCategoryLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCategoryLangListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "lang_id", $_GET)) {        
            return $this->renderWithParent("ek_lang", [
                "lang_id" => $_GET["lang_id"],
            ], [
                "lang_id" => "id",
            ], [
                "lang",
                "langs",
            ], "NullosAdmin_Ekom_Generated_EkLang_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_category_lang",
            'ric' => [
                "lang_id",
				"category_id",
            ],
            'label' => "category lang",
            'labelPlural' => "category langs",
            'route' => "NullosAdmin_Ekom_Generated_EkCategoryLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "category langs",
                'breadcrumb' => "ek_category_lang",
                'form' => "ek_category_lang",
                'list' => "ek_category_lang",
                'ric' => [
                    "lang_id",
					"category_id",
                ],

                "newItemBtnText" => "Add a new category lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCategoryLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCategoryLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCategoryLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
