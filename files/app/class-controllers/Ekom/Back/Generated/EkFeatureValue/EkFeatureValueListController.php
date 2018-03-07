<?php

namespace Controller\Ekom\Back\Generated\EkFeatureValue;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkFeatureValueListController extends EkomBackSimpleFormListController
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
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_feature_value",
            'ric' => [
                "id",
            ],
            'label' => "feature value",
            'labelPlural' => "feature values",
            'route' => "NullosAdmin_Ekom_Generated_EkFeatureValue_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "feature values",
                'breadcrumb' => "ek_feature_value",
                'form' => "ek_feature_value",
                'list' => "ek_feature_value",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new feature value",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkFeatureValue_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkFeatureValue_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkFeatureValue_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
