<?php

namespace Controller\Ekom\Back\Generated\NulBadge;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class NulBadgeListController extends EkomBackSimpleFormListController
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
            'table' => "nul_badge",
            'ric' => [
                "id",
            ],
            'label' => "badge",
            'labelPlural' => "badges",
            'route' => "NullosAdmin_Ekom_Generated_NulBadge_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "badges",
                'breadcrumb' => "nul_badge",
                'form' => "nul_badge",
                'list' => "nul_badge",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new badge",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_NulBadge_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_NulBadge_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_NulBadge_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
