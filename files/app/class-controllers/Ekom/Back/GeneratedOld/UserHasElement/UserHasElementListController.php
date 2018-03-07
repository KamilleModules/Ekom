<?php

namespace Controller\Ekom\Back\Generated\UserHasElement;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class UserHasElementListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_UserHasElement_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$user_id = $this->getContextFromUrl('user_id');
		$table = "di_user_has_element";
		$context = [
			"user_id" => $user_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("di_user");
            $avatar = QuickPdo::fetch("
select $repr from `di_user` where id=:user_id 
            ", [
				"user_id" => $user_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Elements for user \"$avatar\"",
            'breadcrumb' => "user_has_element",
            'form' => "user_has_element",
            'list' => "user_has_element",
            'ric' => [
                'user_id',
                'element_id',
            ],
            
            "newItemBtnText" => "Add a new element for user \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_UserHasElement_List") . "?form&user_id=$user_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiUser_List",             
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