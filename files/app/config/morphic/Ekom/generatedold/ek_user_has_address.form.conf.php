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
    'user_id',
    'address_id',
];
$user_id = MorphicHelper::getFormContextValue("user_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$address_id = (array_key_exists('address_id', $_GET)) ? $_GET['address_id'] : null;
        
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
    'title' => "User has address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_user_has_address")
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($user_id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.address",
            ]))    
            ->setName("address_id")
            ->setLabel("Address id")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("order")
            ->setLabel("Order")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default_shipping_address")
            ->setLabel("Is_default_shipping_address")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default_billing_address")
            ->setLabel("Is_default_billing_address")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_user_has_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $user_id, $address_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_user_has_address", [
				"user_id" => $fData["user_id"],
				"address_id" => $fData["address_id"],
				"order" => $fData["order"],
				"is_default_shipping_address" => $fData["is_default_shipping_address"],
				"is_default_billing_address" => $fData["is_default_billing_address"],

            ]);
            $form->addNotification("Le/la User has address pour le/la user \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_user_has_address", [
				"order" => $fData["order"],
				"is_default_shipping_address" => $fData["is_default_shipping_address"],
				"is_default_billing_address" => $fData["is_default_billing_address"],

            ], [
				["user_id", "=", $user_id],
				["address_id", "=", $address_id],
            
            ]);
            $form->addNotification("Le/la User has address pour le/la user \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


