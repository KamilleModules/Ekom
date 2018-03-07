<?php

namespace Controller\Ekom\Back\Generated\EutUserTracker;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EutUserTrackerListController extends EkomBackSimpleFormListController
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
            'table' => "eut_user_tracker",
            'ric' => [
                "id",
            ],
            'label' => "user tracker",
            'labelPlural' => "user trackers",
            'route' => "NullosAdmin_Ekom_Generated_EutUserTracker_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user trackers",
                'breadcrumb' => "eut_user_tracker",
                'form' => "eut_user_tracker",
                'list' => "eut_user_tracker",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new user tracker",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EutUserTracker_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EutUserTracker_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EutUserTracker_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
