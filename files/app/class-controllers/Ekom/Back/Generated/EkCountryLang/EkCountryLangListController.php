<?php

namespace Controller\Ekom\Back\Generated\EkCountryLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCountryLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "country_id", $_GET)) {        
            return $this->renderWithParent("ek_country", [
                "country_id" => $_GET["country_id"],
            ], [
                "country_id" => "id",
            ], [
                "country",
                "countries",
            ], "NullosAdmin_Ekom_Generated_EkCountry_List");
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
            'table' => "ek_country_lang",
            'ric' => [
                "country_id",
				"lang_id",
            ],
            'label' => "country lang",
            'labelPlural' => "country langs",
            'route' => "NullosAdmin_Ekom_Generated_EkCountryLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "country langs",
                'breadcrumb' => "ek_country_lang",
                'form' => "ek_country_lang",
                'list' => "ek_country_lang",
                'ric' => [
                    "country_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new country lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCountryLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCountryLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCountryLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
