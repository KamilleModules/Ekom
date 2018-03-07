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

$choice_participant_id = QuickPdo::fetchAll("select id, concat(id, \". \", first_name) as label from ektra_participant", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_training_session_id = QuickPdo::fetchAll("select id, concat(id, \". \", active) as label from ektra_training_session", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'training_session_id',
    'participant_id',
];

$participant_id = (array_key_exists("participant_id", $_GET)) ? $_GET['participant_id'] : null;
$training_session_id = (array_key_exists("training_session_id", $_GET)) ? $_GET['training_session_id'] : null;



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
    'title' => "training session-participant",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_training_session_has_participant")
        ->addControl(SokoChoiceControl::create()
            ->setName("training_session_id")
            ->setLabel("Training session id")
            ->setProperties([
                'readonly' => (null !== $training_session_id),
            ])
            ->setValue($training_session_id)
            ->setChoices($choice_training_session_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("participant_id")
            ->setLabel("Participant id")
            ->setProperties([
                'readonly' => (null !== $participant_id),
            ])
            ->setValue($participant_id)
            ->setChoices($choice_participant_id)),
    'feed' => MorphicHelper::getFeedFunction("ektra_training_session_has_participant"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $training_session_id, $participant_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ektra_training_session_has_participant", [
				"training_session_id" => $fData["training_session_id"],
				"participant_id" => $fData["participant_id"],

            ], '', $ric);
            $form->addNotification("Le/la training session-participant a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ektra_training_session_has_participant", [

            ], [
				["training_session_id", "=", $training_session_id],
				["participant_id", "=", $participant_id],
            
            ]);
            $form->addNotification("Le/la training session-participant a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
