<?php

namespace Controller\Ekom\Back\Generated\EkUserHasUserGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkUserHasUserGroupListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "user_id", $_GET)) {        
            return $this->renderWithParent("ek_user", [
                "user_id" => $_GET["user_id"],
            ], [
                "user_id" => "id",
            ], [
                "user",
                "users",
            ], "NullosAdmin_Ekom_Generated_EkUser_List");
		} elseif ( array_key_exists ( "user_group_id", $_GET)) {        
            return $this->renderWithParent("ek_user_group", [
                "user_group_id" => $_GET["user_group_id"],
            ], [
                "user_group_id" => "id",
            ], [
                "user group",
                "user groups",
            ], "NullosAdmin_Ekom_Generated_EkUserGroup_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_user_has_user_group",
            'ric' => [
                "user_id",
				"user_group_id",
            ],
            'label' => "user-user group",
            'labelPlural' => "user-user groups",
            'route' => "NullosAdmin_Ekom_Generated_EkUserHasUserGroup_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user-user groups",
                'breadcrumb' => "ek_user_has_user_group",
                'form' => "ek_user_has_user_group",
                'list' => "ek_user_has_user_group",
                'ric' => [
                    "user_id",
					"user_group_id",
                ],

                "newItemBtnText" => "Add a new user-user group",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkUserHasUserGroup_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkUserHasUserGroup_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkUserHasUserGroup_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
