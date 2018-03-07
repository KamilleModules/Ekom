<?php

namespace Controller\Ekom\Back\Generated\UserHasBadge;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class UserHasBadgeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_UserHasBadge_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$user_id = $this->getContextFromUrl('user_id');
		$table = "nul_user_has_badge";
		$context = [
			"user_id" => $user_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("nul_user");
            $avatar = QuickPdo::fetch("
select $repr from `nul_user` where id=:user_id 
            ", [
				"user_id" => $user_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Badges for user \"$avatar\"",
            'breadcrumb' => "user_has_badge",
            'form' => "user_has_badge",
            'list' => "user_has_badge",
            'ric' => [
                'user_id',
                'badge_id',
            ],
            
            "newItemBtnText" => "Add a new badge for user \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_UserHasBadge_List") . "?form&user_id=$user_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_NulUser_List",             
            "buttons" => [
                [
                    "label" => "Back to user \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_User_List") . "?id=$user_id",
                ],
            ],
            "context" => [
            	"user_id" => $user_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}