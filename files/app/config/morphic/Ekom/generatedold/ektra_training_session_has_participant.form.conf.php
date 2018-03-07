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


$choice_participant_id = QuickPdo::fetchAll("select id, concat(id, \". \", first_name) as label from kamille.ektra_participant", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'training_session_id',
    'participant_id',
];
$training_session_id = MorphicHelper::getFormContextValue("training_session_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$participant_id = (array_key_exists('participant_id', $_GET)) ? $_GET['participant_id'] : null;
        
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
    'title' => "Training session has participant",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_training_session_has_participant")
        ->addControl(SokoInputControl::create()
            ->setName("training_session_id")
            ->setLabel("Training session id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($training_session_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("participant_id")
            ->setLabel('Participant id')
            ->setChoices($choice_participant_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($participant_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ektra_training_session_has_participant"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $training_session_id, $participant_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ektra_training_session_has_participant", [
				"training_session_id" => $fData["training_session_id"],
				"participant_id" => $fData["participant_id"],

            ]);
            $form->addNotification("Le/la Training session has participant pour le/la training session \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ektra_training_session_has_participant", [

            ], [
				["training_session_id", "=", $training_session_id],
				["participant_id", "=", $participant_id],
            
            ]);
            $form->addNotification("Le/la Training session has participant pour le/la training session \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


