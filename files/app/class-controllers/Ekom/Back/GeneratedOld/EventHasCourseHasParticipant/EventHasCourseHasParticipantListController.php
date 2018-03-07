<?php

namespace Controller\Ekom\Back\Generated\EventHasCourseHasParticipant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EventHasCourseHasParticipantListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EventHasCourseHasParticipant_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$table = "ekev_event_has_course_has_participant";
		$context = [
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_event");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_event` where  
            ", [
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Participants for event \"$avatar\"",
            'breadcrumb' => "event_has_course_has_participant",
            'form' => "event_has_course_has_participant",
            'list' => "event_has_course_has_participant",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new participant for event \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EventHasCourseHasParticipant_List") . "?form&",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",             
            "buttons" => [
                [
                    "label" => "Back to event \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_Event_List") . "?",
                ],
            ],
            "context" => [
            				"avatar" => $avatar

            ],            
            
        ]);
    }


}