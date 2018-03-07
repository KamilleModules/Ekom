<?php

namespace Controller\Ekom\Back\Generated\TABLE69;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class TABLE69ListController extends EkomBackSimpleFormListController
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
            'table' => "TABLE 69",
            'ric' => [
                "IMAGE_FORMATION",
				"NOM_FORMATION",
				"DESCRIPTIF_FORMATION",
				"PRE_REQUIS",
				"INFOS_FORMATION",
				"POUR_QUI",
				"VALIDATION",
				"DUREE_FORMATION",
            ],
            'label' => "table 69",
            'labelPlural' => "table 69s",
            'route' => "NullosAdmin_Ekom_Generated_TABLE69_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "table 69s",
                'breadcrumb' => "TABLE 69",
                'form' => "TABLE 69",
                'list' => "TABLE 69",
                'ric' => [
                    "IMAGE_FORMATION",
					"NOM_FORMATION",
					"DESCRIPTIF_FORMATION",
					"PRE_REQUIS",
					"INFOS_FORMATION",
					"POUR_QUI",
					"VALIDATION",
					"DUREE_FORMATION",
                ],

                "newItemBtnText" => "Add a new table 69",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_TABLE69_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_TABLE69_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_TABLE69_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
