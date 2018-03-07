<?php

namespace Controller\Ekom\Back\Generated\TmTeamHasContact;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class TmTeamHasContactListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "contact_id", $_GET)) {        
            return $this->renderWithParent("tm_contact", [
                "contact_id" => $_GET["contact_id"],
            ], [
                "contact_id" => "id",
            ], [
                "contact",
                "contacts",
            ], "NullosAdmin_Ekom_Generated_TmContact_List");
		} elseif ( array_key_exists ( "team_id", $_GET)) {        
            return $this->renderWithParent("tm_team", [
                "team_id" => $_GET["team_id"],
            ], [
                "team_id" => "id",
            ], [
                "team",
                "teams",
            ], "NullosAdmin_Ekom_Generated_TmTeam_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "tm_team_has_contact",
            'ric' => [
                "team_id",
				"contact_id",
            ],
            'label' => "team-contact",
            'labelPlural' => "team-contacts",
            'route' => "NullosAdmin_Ekom_Generated_TmTeamHasContact_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "team-contacts",
                'breadcrumb' => "tm_team_has_contact",
                'form' => "tm_team_has_contact",
                'list' => "tm_team_has_contact",
                'ric' => [
                    "team_id",
					"contact_id",
                ],

                "newItemBtnText" => "Add a new team-contact",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_TmTeamHasContact_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_TmTeamHasContact_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_TmTeamHasContact_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
