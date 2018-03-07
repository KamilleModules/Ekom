<?php

namespace Controller\Ekom\Back\Generated\DiUserActionHistory;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class DiUserActionHistoryListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "user_id", $_GET)) {        
            return $this->renderWithParent("di_user", [
                "user_id" => $_GET["user_id"],
            ], [
                "user_id" => "id",
            ], [
                "user",
                "users",
            ], "NullosAdmin_Ekom_Generated_DiUser_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "di_user_action_history",
            'ric' => [
                "id",
            ],
            'label' => "user action history",
            'labelPlural' => "user action histories",
            'route' => "NullosAdmin_Ekom_Generated_DiUserActionHistory_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user action histories",
                'breadcrumb' => "di_user_action_history",
                'form' => "di_user_action_history",
                'list' => "di_user_action_history",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new user action history",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiUserActionHistory_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_DiUserActionHistory_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiUserActionHistory_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
