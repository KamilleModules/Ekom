<?php

namespace Controller\Ekom\Back\Generated\EkevEventHasCourseHasParticipant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevEventHasCourseHasParticipantListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "event_has_course_id", $_GET)) {        
            return $this->renderWithParent("ekev_event_has_course", [
                "event_has_course_id" => $_GET["event_has_course_id"],
            ], [
                "event_has_course_id" => "id",
            ], [
                "event-course",
                "event-courses",
            ], "NullosAdmin_Ekom_Generated_EkevEventHasCourse_List");
		} elseif ( array_key_exists ( "participant_id", $_GET)) {        
            return $this->renderWithParent("ekev_participant", [
                "participant_id" => $_GET["participant_id"],
            ], [
                "participant_id" => "id",
            ], [
                "participant",
                "participants",
            ], "NullosAdmin_Ekom_Generated_EkevParticipant_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ekev_event_has_course_has_participant",
            'ric' => [
                "id",
            ],
            'label' => "event-course-participant",
            'labelPlural' => "event-course-participants",
            'route' => "NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "event-course-participants",
                'breadcrumb' => "ekev_event_has_course_has_participant",
                'form' => "ekev_event_has_course_has_participant",
                'list' => "ekev_event_has_course_has_participant",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new event-course-participant",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
