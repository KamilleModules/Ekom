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
    'title' => "Participant",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_participant")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel("User_id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("first_name")
            ->setLabel("First_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("last_name")
            ->setLabel("Last_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone")
            ->setLabel("Phone")
        )
        ->addControl(SokoInputControl::create()
            ->setName("email")
            ->setLabel("Email")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ektra_participant"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ektra_participant", [
				"user_id" => $fData["user_id"],
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"phone" => $fData["phone"],
				"email" => $fData["email"],

            ]);
            $form->addNotification("Le/la Participant a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ektra_participant", [
				"user_id" => $fData["user_id"],
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"phone" => $fData["phone"],
				"email" => $fData["email"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Participant a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


