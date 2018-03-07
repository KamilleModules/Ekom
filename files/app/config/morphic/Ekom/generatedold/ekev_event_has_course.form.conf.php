<?php

use QuickPdo\QuickPdo;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use SokoForm\Form\SokoFormInterface;
use SokoForm\Form\SokoForm;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoBooleanChoiceControl;
use Module\Ekom\Utils\E;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\SokoForm\Control\EkomSokoDateControl;


$choice_event_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ekev_event", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_course_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ekev_course", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_presenter_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ekev_presenter_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric = [
    'id',
];

$id = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
$event_id = (array_key_exists("event_id", $_GET)) ? $_GET['event_id'] : null;
$course_id = (array_key_exists("course_id", $_GET)) ? $_GET['course_id'] : null;
$presenter_group_id = (array_key_exists("presenter_group_id", $_GET)) ? $_GET['presenter_group_id'] : null;


$avatar = (array_key_exists("avatar", $context)) ? $context['avatar'] : null;

//--------------------------------------------
// UPDATE|INSERT MODE
//--------------------------------------------
$isUpdate = MorphicHelper::getIsUpdate($ric);

//--------------------------------------------
// FORM
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Event has course",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_event_has_course")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("event_id")
            ->setLabel('Event id')
            ->setChoices($choice_event_id)
            ->setProperties([
                'readonly' => (null !== $event_id),
            ])

        )
        ->addControl(SokoChoiceControl::create()
            ->setName("course_id")
            ->setLabel('Course id')
            ->setChoices($choice_course_id)
            ->setProperties([
                'readonly' => (null !== $course_id),
            ])

        )
        ->addControl(EkomSokoDateControl::create()
            ->setName("date")
            ->setLabel('Date')
        )
        ->addControl(SokoInputControl::create()
            ->setName("start_time")
            ->setLabel("Start_time")
        )
        ->addControl(SokoInputControl::create()
            ->setName("end_time")
            ->setLabel("End_time")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("presenter_group_id")
            ->setLabel('Presenter group id')
            ->setChoices($choice_presenter_group_id)
            ->setProperties([
                'readonly' => (null !== $presenter_group_id),
            ])

        )
        ->addControl(SokoInputControl::create()
            ->setName("capacity")
            ->setLabel("Capacity")
        )
    ,
    'feed' => MorphicHelper::getFeedFunction("ekev_event_has_course"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $id) {

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_event_has_course", [
                "event_id" => $fData["event_id"],
                "course_id" => $fData["course_id"],
                "date" => $fData["date"],
                "start_time" => $fData["start_time"],
                "end_time" => $fData["end_time"],
                "presenter_group_id" => $fData["presenter_group_id"],
                "capacity" => $fData["capacity"],

            ]);
            $form->addNotification("Le event-course a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_event_has_course", [
                "event_id" => $fData["event_id"],
                "course_id" => $fData["course_id"],
                "date" => $fData["date"],
                "start_time" => $fData["start_time"],
                "end_time" => $fData["end_time"],
                "presenter_group_id" => $fData["presenter_group_id"],
                "capacity" => $fData["capacity"],

            ], [
                ["id", "=", $id],
            ]);
            $form->addNotification("Le event-course a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
    //--------------------------------------------
    // IF HAS CONTEXT
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List") . "?event_has_course_id=$id",
                    "text" => "Voir les participants de ce/cette Event has course",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


