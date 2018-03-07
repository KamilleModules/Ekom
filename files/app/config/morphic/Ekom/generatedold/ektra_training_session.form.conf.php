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


$choice_training_id = QuickPdo::fetchAll("select id, concat(id, \". \", prerequisites) as label from kamille.ektra_training", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_trainer_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ektra_trainer_group", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_city_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ektra_city", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'id',
];
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
    'title' => "Training session",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_training_session")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("training_id")
            ->setLabel('Training id')
            ->setChoices($choice_training_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("trainer_group_id")
            ->setLabel('Trainer group id')
            ->setChoices($choice_trainer_group_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("city_id")
            ->setLabel('City id')
            ->setChoices($choice_city_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(EkomSokoDateControl::create()
            ->setName("start_date")
            ->setLabel('Start date')
        )
        ->addControl(EkomSokoDateControl::create()
            ->setName("end_date")
            ->setLabel('End date')
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default")
            ->setLabel("Is_default")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("capacity")
            ->setLabel("Capacity")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ektra_training_session"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ektra_training_session", [
				"training_id" => $fData["training_id"],
				"trainer_group_id" => $fData["trainer_group_id"],
				"city_id" => $fData["city_id"],
				"start_date" => $fData["start_date"],
				"end_date" => $fData["end_date"],
				"is_default" => $fData["is_default"],
				"capacity" => $fData["capacity"],
				"active" => $fData["active"],

            ]);
            $form->addNotification("Le/la Training session a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ektra_training_session", [
				"training_id" => $fData["training_id"],
				"trainer_group_id" => $fData["trainer_group_id"],
				"city_id" => $fData["city_id"],
				"start_date" => $fData["start_date"],
				"end_date" => $fData["end_date"],
				"is_default" => $fData["is_default"],
				"capacity" => $fData["capacity"],
				"active" => $fData["active"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Training session a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List") . "?training_session_id=$id",
                    "text" => "Voir les participants de ce/cette Training session",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


