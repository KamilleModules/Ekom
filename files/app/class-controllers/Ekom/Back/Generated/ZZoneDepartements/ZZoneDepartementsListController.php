<?php

namespace Controller\Ekom\Back\Generated\ZZoneDepartements;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class ZZoneDepartementsListController extends EkomBackSimpleFormListController
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
            'table' => "z_zone_departements",
            'ric' => [
                "z1",
				"z2",
				"z3",
				"z4",
				"z5",
            ],
            'label' => "zone departements",
            'labelPlural' => "zone departementses",
            'route' => "NullosAdmin_Ekom_Generated_ZZoneDepartements_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "zone departementses",
                'breadcrumb' => "z_zone_departements",
                'form' => "z_zone_departements",
                'list' => "z_zone_departements",
                'ric' => [
                    "z1",
					"z2",
					"z3",
					"z4",
					"z5",
                ],

                "newItemBtnText" => "Add a new zone departements",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_ZZoneDepartements_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_ZZoneDepartements_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_ZZoneDepartements_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
