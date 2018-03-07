<?php

namespace Controller\Ekom\Back\Generated\EkevEventHasCourseHasParticipant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevEventHasCourseHasParticipantListController extends EkomBackSimpleFormListController
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

        // maybe will be removed...
        return $this->renderWithNoParent();
    }



    //--------------------------------------------
    // foreach fks
    //--------------------------------------------
    protected function renderWithEkevEventParent($event_id)
    {
        $route = "NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------
        $event_has_course_id = $this->getContextFromUrl('id');
        $table = "ekev_event_has_course_has_participant";
        $context = [
            "event_has_course_id" => $event_has_course_id,
        ];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_event");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_event` where id=:event_has_course_id 
            ", [
                "event_has_course_id" => $event_has_course_id,

            ], \PDO::FETCH_COLUMN);
        }


        return $this->doRenderFormList([
            'title' => "Participants for event \"$avatar\"",
            'breadcrumb' => "ekev_event_has_course_has_participant",
            'form' => "ekev_event_has_course_has_participant",
            'list' => "ekev_event_has_course_has_participant",
            'ric' => [
                'id',
            ],

            "newItemBtnText" => "Add a new participant for event \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List") . "?form&event_has_course_id=$event_has_course_id",

            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",
            "buttons" => [
                [
                    "label" => "Back to event \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEvent_List") . "?id=$event_has_course_id",
                ],
            ],
            "context" => [
                "id" => $event_has_course_id,
                "avatar" => $avatar

            ],

        ]);
    }

    protected function renderWithEkevCourseParent($course_id)
    {
        $route = "NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------
        $event_has_course_id = $this->getContextFromUrl('id');
        $table = "ekev_event_has_course_has_participant";
        $context = [
            "event_has_course_id" => $event_has_course_id,
        ];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_event");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_event` where id=:event_has_course_id 
            ", [
                "event_has_course_id" => $event_has_course_id,

            ], \PDO::FETCH_COLUMN);
        }


        return $this->doRenderFormList([
            'title' => "Participants for event \"$avatar\"",
            'breadcrumb' => "ekev_event_has_course_has_participant",
            'form' => "ekev_event_has_course_has_participant",
            'list' => "ekev_event_has_course_has_participant",
            'ric' => [
                'id',
            ],

            "newItemBtnText" => "Add a new participant for event \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List") . "?form&event_has_course_id=$event_has_course_id",

            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",
            "buttons" => [
                [
                    "label" => "Back to event \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEvent_List") . "?id=$event_has_course_id",
                ],
            ],
            "context" => [
                "id" => $event_has_course_id,
                "avatar" => $avatar

            ],

        ]);
    }

    protected function renderWithEkevPresenterGroupParent($presenter_group_id)
    {
        $route = "NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------
        $event_has_course_id = $this->getContextFromUrl('id');
        $table = "ekev_event_has_course_has_participant";
        $context = [
            "event_has_course_id" => $event_has_course_id,
        ];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ekev_event");
            $avatar = QuickPdo::fetch("
select $repr from `ekev_event` where id=:event_has_course_id 
            ", [
                "event_has_course_id" => $event_has_course_id,

            ], \PDO::FETCH_COLUMN);
        }


        return $this->doRenderFormList([
            'title' => "Participants for event \"$avatar\"",
            'breadcrumb' => "ekev_event_has_course_has_participant",
            'form' => "ekev_event_has_course_has_participant",
            'list' => "ekev_event_has_course_has_participant",
            'ric' => [
                'id',
            ],

            "newItemBtnText" => "Add a new participant for event \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List") . "?form&event_has_course_id=$event_has_course_id",

            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",
            "buttons" => [
                [
                    "label" => "Back to event \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEvent_List") . "?id=$event_has_course_id",
                ],
            ],
            "context" => [
                "id" => $event_has_course_id,
                "avatar" => $avatar

            ],

        ]);
    }
    // end foreach




    protected function renderWithNoParent()
    {
        /**
         * todo
         */
    }

}