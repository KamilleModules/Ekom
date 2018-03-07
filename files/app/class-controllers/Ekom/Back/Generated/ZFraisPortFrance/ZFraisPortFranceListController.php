<?php

namespace Controller\Ekom\Back\Generated\ZFraisPortFrance;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class ZFraisPortFranceListController extends EkomBackSimpleFormListController
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
            'table' => "z_frais_port_france",
            'ric' => [
                "max_kg",
				"z1",
				"z2",
				"z3",
				"z4",
				"z5",
            ],
            'label' => "frais port france",
            'labelPlural' => "frais port frances",
            'route' => "NullosAdmin_Ekom_Generated_ZFraisPortFrance_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "frais port frances",
                'breadcrumb' => "z_frais_port_france",
                'form' => "z_frais_port_france",
                'list' => "z_frais_port_france",
                'ric' => [
                    "max_kg",
					"z1",
					"z2",
					"z3",
					"z4",
					"z5",
                ],

                "newItemBtnText" => "Add a new frais port france",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_ZFraisPortFrance_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_ZFraisPortFrance_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_ZFraisPortFrance_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
