<?php

namespace Controller\Ekom\Back\Generated\TmpFormations;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class TmpFormationsListController extends EkomBackSimpleFormListController
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
            'table' => "tmp_formations",
            'ric' => [
                "reference",
				"date",
				"location",
            ],
            'label' => "formations",
            'labelPlural' => "formationses",
            'route' => "NullosAdmin_Ekom_Generated_TmpFormations_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "formationses",
                'breadcrumb' => "tmp_formations",
                'form' => "tmp_formations",
                'list' => "tmp_formations",
                'ric' => [
                    "reference",
					"date",
					"location",
                ],

                "newItemBtnText" => "Add a new formations",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_TmpFormations_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_TmpFormations_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_TmpFormations_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
