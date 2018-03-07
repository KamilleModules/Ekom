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


$choice_trainer_id = QuickPdo::fetchAll("select id, concat(id, \". \", pseudo) as label from kamille.ektra_trainer", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'trainer_group_id',
    'trainer_id',
];
$trainer_group_id = MorphicHelper::getFormContextValue("trainer_group_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$trainer_id = (array_key_exists('trainer_id', $_GET)) ? $_GET['trainer_id'] : null;
        
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
    'title' => "Trainer group has trainer",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_trainer_group_has_trainer")
        ->addControl(SokoInputControl::create()
            ->setName("trainer_group_id")
            ->setLabel("Trainer group id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($trainer_group_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("trainer_id")
            ->setLabel('Trainer id')
            ->setChoices($choice_trainer_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($trainer_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ektra_trainer_group_has_trainer"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $trainer_group_id, $trainer_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ektra_trainer_group_has_trainer", [
				"trainer_group_id" => $fData["trainer_group_id"],
				"trainer_id" => $fData["trainer_id"],

            ]);
            $form->addNotification("Le/la Trainer group has trainer pour le/la trainer group \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ektra_trainer_group_has_trainer", [

            ], [
				["trainer_group_id", "=", $trainer_group_id],
				["trainer_id", "=", $trainer_id],
            
            ]);
            $form->addNotification("Le/la Trainer group has trainer pour le/la trainer group \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


