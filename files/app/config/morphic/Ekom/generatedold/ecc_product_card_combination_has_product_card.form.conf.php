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
$product_card_combination_id = MorphicHelper::getFormContextValue("product_card_combination_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
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
    'title' => "Product card combination has product card",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ecc_product_card_combination_has_product_card")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("product_card_combination_id")
            ->setLabel("Product card combination id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($product_card_combination_id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product_card",
            ]))    
            ->setName("product_card_id")
            ->setLabel("Product card id")
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))    
            ->setName("product_id")
            ->setLabel("Product id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("quantity")
            ->setLabel("Quantity")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ecc_product_card_combination_has_product_card"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ecc_product_card_combination_has_product_card", [
				"product_card_combination_id" => $fData["product_card_combination_id"],
				"product_card_id" => $fData["product_card_id"],
				"product_id" => $fData["product_id"],
				"quantity" => $fData["quantity"],

            ]);
            $form->addNotification("Le/la Product card combination has product card pour le/la product card combination \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ecc_product_card_combination_has_product_card", [
				"product_card_combination_id" => $fData["product_card_combination_id"],
				"product_card_id" => $fData["product_card_id"],
				"product_id" => $fData["product_id"],
				"quantity" => $fData["quantity"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Product card combination has product card pour le/la product card combination \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


