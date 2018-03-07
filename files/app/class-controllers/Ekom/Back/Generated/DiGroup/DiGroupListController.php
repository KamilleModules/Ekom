<?php

namespace Controller\Ekom\Back\Generated\DiGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class DiGroupListController extends EkomBackSimpleFormListController
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
            'table' => "di_group",
            'ric' => [
                "id",
            ],
            'label' => "group",
            'labelPlural' => "groups",
            'route' => "NullosAdmin_Ekom_Generated_DiGroup_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "groups",
                'breadcrumb' => "di_group",
                'form' => "di_group",
                'list' => "di_group",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new group",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiGroup_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_DiGroup_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiGroup_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
