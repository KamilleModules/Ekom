<?php

namespace Controller\Ekom\Back\Generated\DiUser;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class DiUserListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "group_id", $_GET)) {        
            return $this->renderWithParent("di_group", [
                "group_id" => $_GET["group_id"],
            ], [
                "group_id" => "id",
            ], [
                "group",
                "groups",
            ], "NullosAdmin_Ekom_Generated_DiGroup_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "di_user",
            'ric' => [
                "id",
            ],
            'label' => "user",
            'labelPlural' => "users",
            'route' => "NullosAdmin_Ekom_Generated_DiUser_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "users",
                'breadcrumb' => "di_user",
                'form' => "di_user",
                'list' => "di_user",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new user",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiUser_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_DiUser_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiUser_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
