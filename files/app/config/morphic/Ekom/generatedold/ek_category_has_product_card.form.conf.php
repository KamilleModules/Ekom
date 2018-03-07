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
    'category_id',
    'product_card_id',
];
$category_id = MorphicHelper::getFormContextValue("category_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$product_card_id = (array_key_exists('product_card_id', $_GET)) ? $_GET['product_card_id'] : null;
        
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
    'title' => "Category has product card",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_category_has_product_card")
        ->addControl(SokoInputControl::create()
            ->setName("category_id")
            ->setLabel("Category id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($category_id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product_card",
            ]))    
            ->setName("product_card_id")
            ->setLabel("Product card id")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_category_has_product_card"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $category_id, $product_card_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_category_has_product_card", [
				"category_id" => $fData["category_id"],
				"product_card_id" => $fData["product_card_id"],

            ]);
            $form->addNotification("Le/la Category has product card pour le/la category \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_category_has_product_card", [

            ], [
				["category_id", "=", $category_id],
				["product_card_id", "=", $product_card_id],
            
            ]);
            $form->addNotification("Le/la Category has product card pour le/la category \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


