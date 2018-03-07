<?php

namespace Controller\Ekom\Back\Generated\EkevEventHasCourse;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class OldEkevEventHasCourseListController extends EkomBackSimpleFormListController
{


    public function render()
    {


        //--------------------------------------------
        // REDIRECTING ACCORDING TO FKEYS
        //--------------------------------------------
        if (array_key_exists("event_id", $_GET)) {
            return $this->renderWithEkevEventParent($_GET["event_id"]);
        } elseif (array_key_exists("course_id", $_GET)) {
            return $this->renderWithEkevCourseParent($_GET["course_id"]);
        } elseif (array_key_exists("presenter_group_id", $_GET)) {
            return $this->renderWithEkevPresenterGroupParent($_GET["presenter_group_id"]);
        }

        // maybe will be removed, or put this in EkomConfig...
        if ('hasAdminPower') {
            return $this->renderWithNoParent();
        }
    }



    //--------------------------------------------
    // foreach fks
    //--------------------------------------------
    protected function renderWithEkevEventParent($event_id)
    {
        $table = "ekev_event_has_course";

        // this is the list context
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
            'title' => "event-courses for event \"$avatar\"",
            'breadcrumb' => "ekev_event_has_course",
            'form' => "ekev_event_has_course",
            'list' => "ekev_event_has_course",
            'ric' => [
                'id',
            ],

            "newItemBtnText" => "Add a new course for event \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourse_List") . "?form&event_id=$event_id",

            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",
            "buttons" => [
                [
                    "label" => "Back to event \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEvent_List") . "?id=$event_id",
                ],
            ],
            "context" => [
                "event_id" => $event_id,
                "avatar" => $avatar,
                "_parentKeys" => ["event_id"],
            ],

        ]);
    }

    protected function renderWithEkevCourseParent($course_id)
    {
        $table = "ekev_event_has_course";
        $context = [
            "course_id" => $course_id,
        ];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_event");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_course` where id=:course_id 
            ", [
                "course_id" => $course_id,

            ], \PDO::FETCH_COLUMN);
        }

        return $this->doRenderFormList([
            'title' => "event-courses for course \"$avatar\"",
            'breadcrumb' => "ekev_event_has_course",
            'form' => "ekev_event_has_course",
            'list' => "ekev_event_has_course",
            'ric' => [
                'id',
            ],

            "newItemBtnText" => "Add a new course for event \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourse_List") . "?form&course_id=$course_id",

            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevCourse_List",
            "buttons" => [
                [
                    "label" => "Back to course \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevCourse_List") . "?id=$course_id",
                ],
            ],
            "context" => [
                "course_id" => $course_id,
                "avatar" => $avatar,
                "_parentKeys" => ["course_id"],
            ],

        ]);
    }

    protected function renderWithEkevPresenterGroupParent($presenter_group_id)
    {
        $table = "ekev_event_has_course";
        $context = [
            "presenter_group_id" => $presenter_group_id,
        ];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_event");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_presenter_group` where id=:presenter_group_id 
            ", [
                "presenter_group_id" => $presenter_group_id,

            ], \PDO::FETCH_COLUMN);
        }


        return $this->doRenderFormList([
            'title' => "event-courses for presenter group \"$avatar\"",
            'breadcrumb' => "ekev_event_has_course",
            'form' => "ekev_event_has_course",
            'list' => "ekev_event_has_course",
            'ric' => [
                'id',
            ],

            "newItemBtnText" => "Add a new event-course for presenter group \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourse_List") . "?form&presenter_group_id=$presenter_group_id",

            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",
            "buttons" => [
                [
                    "label" => "Back to event \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevPresenterGroup_List") . "?id=$presenter_group_id",
                ],
            ],
            "context" => [
                "presenter_group_id" => $presenter_group_id,
                "avatar" => $avatar,
                "_parentKeys" => ["presenter_group_id"],
            ],

        ]);
    }

    // end foreach


    protected function renderWithNoParent()
    {

        return $this->doRenderFormList([
            'title' => "event-courses",
            'breadcrumb' => "ekev_event_has_course",
            'form' => "ekev_event_has_course",
            'list' => "ekev_event_has_course",
            'ric' => [
                'id',
            ],

            "newItemBtnText" => "Add a new event-course",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourse_List") . "?form",

            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",
            "context" => [],

        ]);

    }


}