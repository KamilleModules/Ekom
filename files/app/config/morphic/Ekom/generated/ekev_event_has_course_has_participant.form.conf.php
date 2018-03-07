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

$choice_event_has_course_id = QuickPdo::fetchAll("select id, concat(id, \". \", start_time) as label from ekev_event_has_course", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_participant_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from ekev_participant", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$event_has_course_id = (array_key_exists("event_has_course_id", $_GET)) ? $_GET['event_has_course_id'] : null;
$participant_id = (array_key_exists("participant_id", $_GET)) ? $_GET['participant_id'] : null;



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
    'title' => "event-course-participant",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_event_has_course_has_participant")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("event_has_course_id")
            ->setLabel("Event has course id")
            ->setProperties([
                'readonly' => (null !== $event_has_course_id),
            ])
            ->setValue($event_has_course_id)
            ->setChoices($choice_event_has_course_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("participant_id")
            ->setLabel("Participant id")
            ->setProperties([
                'readonly' => (null !== $participant_id),
            ])
            ->setValue($participant_id)
            ->setChoices($choice_participant_id))
        ->addControl(SokoInputControl::create()
            ->setName("sponsor_user_id")
            ->setLabel("Sponsor_user_id")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("datetime")
            ->setLabel("Datetime")
            ->addProperties([
                "required" => true,                       
            ])
                        
        ),
    'feed' => MorphicHelper::getFeedFunction("ekev_event_has_course_has_participant"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ekev_event_has_course_has_participant", [
				"event_has_course_id" => $fData["event_has_course_id"],
				"participant_id" => $fData["participant_id"],
				"sponsor_user_id" => $fData["sponsor_user_id"],
				"datetime" => $fData["datetime"],

            ], '', $ric);
            $form->addNotification("Le/la event-course-participant a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ekev_event_has_course_has_participant", [
				"event_has_course_id" => $fData["event_has_course_id"],
				"participant_id" => $fData["participant_id"],
				"sponsor_user_id" => $fData["sponsor_user_id"],
				"datetime" => $fData["datetime"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la event-course-participant a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
