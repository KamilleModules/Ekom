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
    'title' => "User action history",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_user_action_history")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.user",
            ]))    
            ->setName("user_id")
            ->setLabel("User id")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("action_date")
            ->setLabel("Action_date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("action_name")
            ->setLabel("Action_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("action_value")
            ->setLabel("Action_value")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("di_user_action_history"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("di_user_action_history", [
				"user_id" => $fData["user_id"],
				"action_date" => $fData["action_date"],
				"action_name" => $fData["action_name"],
				"action_value" => $fData["action_value"],

            ]);
            $form->addNotification("Le/la User action history a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("di_user_action_history", [
				"user_id" => $fData["user_id"],
				"action_date" => $fData["action_date"],
				"action_name" => $fData["action_name"],
				"action_value" => $fData["action_value"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la User action history a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


