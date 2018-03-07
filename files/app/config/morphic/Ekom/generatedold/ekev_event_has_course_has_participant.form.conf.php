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


$choice_event_has_course_id = QuickPdo::fetchAll("select id, concat(id, \". \", start_time) as label from kamille.ekev_event_has_course", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_participant_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from kamille.ekev_participant", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'id',
];
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$id = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
        
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
    'title' => "Event has course has participant",
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
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("event_has_course_id")
            ->setLabel('Event has course id')
            ->setChoices($choice_event_has_course_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("participant_id")
            ->setLabel('Participant id')
            ->setChoices($choice_participant_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
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
                        
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ekev_event_has_course_has_participant"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_event_has_course_has_participant", [
				"event_has_course_id" => $fData["event_has_course_id"],
				"participant_id" => $fData["participant_id"],
				"sponsor_user_id" => $fData["sponsor_user_id"],
				"datetime" => $fData["datetime"],

            ]);
            $form->addNotification("Le/la Event has course has participant pour le/la event \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_event_has_course_has_participant", [
				"event_has_course_id" => $fData["event_has_course_id"],
				"participant_id" => $fData["participant_id"],
				"sponsor_user_id" => $fData["sponsor_user_id"],
				"datetime" => $fData["datetime"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Event has course has participant pour le/la event \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


