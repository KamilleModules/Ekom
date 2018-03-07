<?php

namespace Controller\Ekom\Back\Generated\EkevEventHasCourse;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevEventHasCourseListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "course_id", $_GET)) {        
            return $this->renderWithParent("ekev_course", [
                "course_id" => $_GET["course_id"],
            ], [
                "course_id" => "id",
            ], [
                "course",
                "courses",
            ], "NullosAdmin_Ekom_Generated_EkevCourse_List");
		} elseif ( array_key_exists ( "event_id", $_GET)) {        
            return $this->renderWithParent("ekev_event", [
                "event_id" => $_GET["event_id"],
            ], [
                "event_id" => "id",
            ], [
                "event",
                "events",
            ], "NullosAdmin_Ekom_Generated_EkevEvent_List");
		} elseif ( array_key_exists ( "presenter_group_id", $_GET)) {        
            return $this->renderWithParent("ekev_presenter_group", [
                "presenter_group_id" => $_GET["presenter_group_id"],
            ], [
                "presenter_group_id" => "id",
            ], [
                "presenter group",
                "presenter groups",
            ], "NullosAdmin_Ekom_Generated_EkevPresenterGroup_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ekev_event_has_course",
            'ric' => [
                "id",
            ],
            'label' => "event-course",
            'labelPlural' => "event-courses",
            'route' => "NullosAdmin_Ekom_Generated_EkevEventHasCourse_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "event-courses",
                'breadcrumb' => "ekev_event_has_course",
                'form' => "ekev_event_has_course",
                'list' => "ekev_event_has_course",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new event-course",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourse_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevEventHasCourse_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEventHasCourse_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
