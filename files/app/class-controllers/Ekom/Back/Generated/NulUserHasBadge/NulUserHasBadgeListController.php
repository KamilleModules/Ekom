<?php

namespace Controller\Ekom\Back\Generated\NulUserHasBadge;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class NulUserHasBadgeListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "badge_id", $_GET)) {        
            return $this->renderWithParent("nul_badge", [
                "badge_id" => $_GET["badge_id"],
            ], [
                "badge_id" => "id",
            ], [
                "badge",
                "badges",
            ], "NullosAdmin_Ekom_Generated_NulBadge_List");
		} elseif ( array_key_exists ( "user_id", $_GET)) {        
            return $this->renderWithParent("nul_user", [
                "user_id" => $_GET["user_id"],
            ], [
                "user_id" => "id",
            ], [
                "user",
                "users",
            ], "NullosAdmin_Ekom_Generated_NulUser_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "nul_user_has_badge",
            'ric' => [
                "user_id",
				"badge_id",
            ],
            'label' => "user-badge",
            'labelPlural' => "user-badges",
            'route' => "NullosAdmin_Ekom_Generated_NulUserHasBadge_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user-badges",
                'breadcrumb' => "nul_user_has_badge",
                'form' => "nul_user_has_badge",
                'list' => "nul_user_has_badge",
                'ric' => [
                    "user_id",
					"badge_id",
                ],

                "newItemBtnText" => "Add a new user-badge",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_NulUserHasBadge_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_NulUserHasBadge_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_NulUserHasBadge_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
