<?php

namespace Controller\Ekom\Back\Generated\EkTaxLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkTaxLangListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "tax_id", $_GET)) {        
            return $this->renderWithParent("ek_tax", [
                "tax_id" => $_GET["tax_id"],
            ], [
                "tax_id" => "id",
            ], [
                "tax",
                "taxes",
            ], "NullosAdmin_Ekom_Generated_EkTax_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_tax_lang",
            'ric' => [
                "tax_id",
				"lang_id",
            ],
            'label' => "tax lang",
            'labelPlural' => "tax langs",
            'route' => "NullosAdmin_Ekom_Generated_EkTaxLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "tax langs",
                'breadcrumb' => "ek_tax_lang",
                'form' => "ek_tax_lang",
                'list' => "ek_tax_lang",
                'ric' => [
                    "tax_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new tax lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkTaxLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkTaxLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkTaxLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
