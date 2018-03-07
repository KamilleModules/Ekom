<?php

namespace Controller\Ekom\Back\Generated\EkFeatureLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkFeatureLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "feature_id", $_GET)) {        
            return $this->renderWithParent("ek_feature", [
                "feature_id" => $_GET["feature_id"],
            ], [
                "feature_id" => "id",
            ], [
                "feature",
                "features",
            ], "NullosAdmin_Ekom_Generated_EkFeature_List");
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
            'table' => "ek_feature_lang",
            'ric' => [
                "lang_id",
				"feature_id",
            ],
            'label' => "feature lang",
            'labelPlural' => "feature langs",
            'route' => "NullosAdmin_Ekom_Generated_EkFeatureLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "feature langs",
                'breadcrumb' => "ek_feature_lang",
                'form' => "ek_feature_lang",
                'list' => "ek_feature_lang",
                'ric' => [
                    "lang_id",
					"feature_id",
                ],

                "newItemBtnText" => "Add a new feature lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkFeatureLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkFeatureLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkFeatureLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
