<?php

namespace Controller\Ekom\Back\Generated\TmTeamHasContact;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TmTeamHasContactListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TmTeamHasContact_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$team_id = $this->getContextFromUrl('team_id');
		$table = "tm_team_has_contact";
		$context = [
			"team_id" => $team_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("tm_team");
            $avatar = QuickPdo::fetch("
select $repr from `tm_team` where id=:team_id 
            ", [
				"team_id" => $team_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Contacts for team \"$avatar\"",
            'breadcrumb' => "tm_team_has_contact",
            'form' => "tm_team_has_contact",
            'list' => "tm_team_has_contact",
            'ric' => [
                'team_id',
                'contact_id',
            ],
            
            "newItemBtnText" => "Add a new contact for team \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_TmTeamHasContact_List") . "?form&team_id=$team_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_TmTeam_List",             
            "buttons" => [
                [
                    "label" => "Back to team \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_TmTeam_List") . "?id=$team_id",
                ],
            ],
            "context" => [
            	"team_id" => $team_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}