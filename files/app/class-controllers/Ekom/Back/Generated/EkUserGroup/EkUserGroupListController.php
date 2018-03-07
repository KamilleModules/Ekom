<?php

namespace Controller\Ekom\Back\Generated\EkUserGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkUserGroupListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "shop_id", $_GET)) {        
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_user_group",
            'ric' => [
                "id",
            ],
            'label' => "user group",
            'labelPlural' => "user groups",
            'route' => "NullosAdmin_Ekom_Generated_EkUserGroup_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user groups",
                'breadcrumb' => "ek_user_group",
                'form' => "ek_user_group",
                'list' => "ek_user_group",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new user group",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkUserGroup_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkUserGroup_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkUserGroup_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
