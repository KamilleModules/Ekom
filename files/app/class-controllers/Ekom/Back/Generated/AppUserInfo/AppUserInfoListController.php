<?php

namespace Controller\Ekom\Back\Generated\AppUserInfo;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class AppUserInfoListController extends EkomBackSimpleFormListController
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
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "app_user_info",
            'ric' => [
                "user_id",
            ],
            'label' => "user info",
            'labelPlural' => "user infos",
            'route' => "NullosAdmin_Ekom_Generated_AppUserInfo_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user infos",
                'breadcrumb' => "app_user_info",
                'form' => "app_user_info",
                'list' => "app_user_info",
                'ric' => [
                    "user_id",
                ],

                "newItemBtnText" => "Add a new user info",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_AppUserInfo_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_AppUserInfo_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_AppUserInfo_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
