<?php

namespace Controller\Ekom\Back\Generated\EventHasCourse;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EventHasCourseListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EventHasCourse_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$event_id = $this->getContextFromUrl('event_id');
		$table = "ekev_event_has_course";
		$context = [
			"event_id" => $event_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_event");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_event` where id=:event_id 
            ", [
				"event_id" => $event_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Courses for event \"$avatar\"",
            'breadcrumb' => "event_has_course",
            'form' => "event_has_course",
            'list' => "event_has_course",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new course for event \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EventHasCourse_List") . "?form&event_id=$event_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",             
            "buttons" => [
                [
                    "label" => "Back to event \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_Event_List") . "?id=$event_id",
                ],
            ],
            "context" => [
            	"event_id" => $event_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}