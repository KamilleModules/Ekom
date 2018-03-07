<?php

namespace Controller\Ekom\Back\Generated\TmTeam;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class TmTeamListController extends EkomBackSimpleFormListController
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
            'table' => "tm_team",
            'ric' => [
                "id",
            ],
            'label' => "team",
            'labelPlural' => "teams",
            'route' => "NullosAdmin_Ekom_Generated_TmTeam_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "teams",
                'breadcrumb' => "tm_team",
                'form' => "tm_team",
                'list' => "tm_team",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new team",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_TmTeam_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_TmTeam_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_TmTeam_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
