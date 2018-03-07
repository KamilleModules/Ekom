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

// inferred data (can be overridden by fkeys)
$shop_id = EkomNullosUser::getEkomValue("shop_id");
$lang_id = EkomNullosUser::getEkomValue("lang_id");
$currency_id = EkomNullosUser::getEkomValue("currency_id");

$choice_course_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ekev_course", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_event_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ekev_event", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_presenter_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ekev_presenter_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$course_id = (array_key_exists("course_id", $_GET)) ? $_GET['course_id'] : null;
$event_id = (array_key_exists("event_id", $_GET)) ? $_GET['event_id'] : null;
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
    'title' => "event-course",
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
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("event_id")
            ->setLabel("Event id")
            ->setProperties([
                'readonly' => (null !== $event_id),
            ])
            ->setValue($event_id)
            ->setChoices($choice_event_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("course_id")
            ->setLabel("Course id")
            ->setProperties([
                'readonly' => (null !== $course_id),
            ])
            ->setValue($course_id)
            ->setChoices($choice_course_id))
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
            ->setLabel("Presenter group id")
            ->setProperties([
                'readonly' => (null !== $presenter_group_id),
            ])
            ->setValue($presenter_group_id)
            ->setChoices($choice_presenter_group_id))
        ->addControl(SokoInputControl::create()
            ->setName("capacity")
            ->setLabel("Capacity")
        ),
    'feed' => MorphicHelper::getFeedFunction("ekev_event_has_course"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ekev_event_has_course", [
				"event_id" => $fData["event_id"],
				"course_id" => $fData["course_id"],
				"date" => $fData["date"],
				"start_time" => $fData["start_time"],
				"end_time" => $fData["end_time"],
				"presenter_group_id" => $fData["presenter_group_id"],
				"capacity" => $fData["capacity"],

            ], '', $ric);
            $form->addNotification("Le/la event-course a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
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
            $form->addNotification("Le/la event-course a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,            
    //--------------------------------------------
    // CHILDREN
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List") . "?s&event_has_course_id=$id",
                    "text" => "Voir les event-course-participants",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
