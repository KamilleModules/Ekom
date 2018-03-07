<?php

namespace Controller\Ekom\Back\Generated\EkFeature;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkFeatureListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_feature",
            'ric' => [
                "id",
            ],
            'label' => "feature",
            'labelPlural' => "features",
            'route' => "NullosAdmin_Ekom_Generated_EkFeature_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "features",
                'breadcrumb' => "ek_feature",
                'form' => "ek_feature",
                'list' => "ek_feature",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new feature",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkFeature_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkFeature_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkFeature_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
