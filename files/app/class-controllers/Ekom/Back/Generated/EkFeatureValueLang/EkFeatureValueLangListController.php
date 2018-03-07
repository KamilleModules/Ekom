<?php

namespace Controller\Ekom\Back\Generated\EkFeatureValueLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkFeatureValueLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "feature_value_id", $_GET)) {        
            return $this->renderWithParent("ek_feature_value", [
                "feature_value_id" => $_GET["feature_value_id"],
            ], [
                "feature_value_id" => "id",
            ], [
                "feature value",
                "feature values",
            ], "NullosAdmin_Ekom_Generated_EkFeatureValue_List");
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
            'table' => "ek_feature_value_lang",
            'ric' => [
                "feature_value_id",
				"lang_id",
            ],
            'label' => "feature value lang",
            'labelPlural' => "feature value langs",
            'route' => "NullosAdmin_Ekom_Generated_EkFeatureValueLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "feature value langs",
                'breadcrumb' => "ek_feature_value_lang",
                'form' => "ek_feature_value_lang",
                'list' => "ek_feature_value_lang",
                'ric' => [
                    "feature_value_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new feature value lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkFeatureValueLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkFeatureValueLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkFeatureValueLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
