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
    'title' => "Wallet card",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-pei_wallet_card")
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
        ->addControl(SokoInputControl::create()
            ->setName("type")
            ->setLabel("Type")
        )
        ->addControl(SokoInputControl::create()
            ->setName("last_four_digits")
            ->setLabel("Last_four_digits")
        )
        ->addControl(SokoInputControl::create()
            ->setName("owner")
            ->setLabel("Owner")
        )
        ->addControl(SokoInputControl::create()
            ->setName("expiration_date")
            ->setLabel("Expiration_date")
        )
        ->addControl(SokoInputControl::create()
            ->setName("alias")
            ->setLabel("Alias")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default")
            ->setLabel("Is_default")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("pei_wallet_card"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("pei_wallet_card", [
				"user_id" => $fData["user_id"],
				"type" => $fData["type"],
				"last_four_digits" => $fData["last_four_digits"],
				"owner" => $fData["owner"],
				"expiration_date" => $fData["expiration_date"],
				"alias" => $fData["alias"],
				"active" => $fData["active"],
				"is_default" => $fData["is_default"],

            ]);
            $form->addNotification("Le/la Wallet card a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("pei_wallet_card", [
				"user_id" => $fData["user_id"],
				"type" => $fData["type"],
				"last_four_digits" => $fData["last_four_digits"],
				"owner" => $fData["owner"],
				"expiration_date" => $fData["expiration_date"],
				"alias" => $fData["alias"],
				"active" => $fData["active"],
				"is_default" => $fData["is_default"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Wallet card a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


