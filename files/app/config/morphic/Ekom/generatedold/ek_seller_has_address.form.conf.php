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
    'seller_id',
    'address_id',
];
$seller_id = MorphicHelper::getFormContextValue("seller_id", $context);
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
    'title' => "Seller has address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_seller_has_address")
        ->addControl(SokoInputControl::create()
            ->setName("seller_id")
            ->setLabel("Seller id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($seller_id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.address",
            ]))    
            ->setName("address_id")
            ->setLabel("Address id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_seller_has_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $seller_id, $address_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_seller_has_address", [
				"seller_id" => $fData["seller_id"],
				"address_id" => $fData["address_id"],
				"order" => $fData["order"],

            ]);
            $form->addNotification("Le/la Seller has address pour le/la seller \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_seller_has_address", [
				"order" => $fData["order"],

            ], [
				["seller_id", "=", $seller_id],
				["address_id", "=", $address_id],
            
            ]);
            $form->addNotification("Le/la Seller has address pour le/la seller \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


