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
    'title' => "Password recovery request",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_password_recovery_request")
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
            ->setName("date_created")
            ->setLabel("Date_created")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("code")
            ->setLabel("Code")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_used")
            ->setLabel("Date_used")
            ->addProperties([
                "required" => false,                       
            ])
                        
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_password_recovery_request"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_password_recovery_request", [
				"user_id" => $fData["user_id"],
				"date_created" => $fData["date_created"],
				"code" => $fData["code"],
				"date_used" => $fData["date_used"],

            ]);
            $form->addNotification("Le/la Password recovery request a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_password_recovery_request", [
				"user_id" => $fData["user_id"],
				"date_created" => $fData["date_created"],
				"code" => $fData["code"],
				"date_used" => $fData["date_used"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Password recovery request a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


