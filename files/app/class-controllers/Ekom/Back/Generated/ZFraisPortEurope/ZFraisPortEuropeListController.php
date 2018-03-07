<?php

namespace Controller\Ekom\Back\Generated\ZFraisPortEurope;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class ZFraisPortEuropeListController extends EkomBackSimpleFormListController
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
            'table' => "z_frais_port_europe",
            'ric' => [
                "max_kg",
				"BE",
				"LU",
				"CH",
				"EURZ1",
				"EURZ2",
            ],
            'label' => "frais port europe",
            'labelPlural' => "frais port europes",
            'route' => "NullosAdmin_Ekom_Generated_ZFraisPortEurope_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "frais port europes",
                'breadcrumb' => "z_frais_port_europe",
                'form' => "z_frais_port_europe",
                'list' => "z_frais_port_europe",
                'ric' => [
                    "max_kg",
					"BE",
					"LU",
					"CH",
					"EURZ1",
					"EURZ2",
                ],

                "newItemBtnText" => "Add a new frais port europe",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_ZFraisPortEurope_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_ZFraisPortEurope_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_ZFraisPortEurope_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
