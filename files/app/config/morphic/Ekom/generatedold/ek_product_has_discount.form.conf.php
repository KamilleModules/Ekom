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
    'product_id',
    'discount_id',
];
$product_id = MorphicHelper::getFormContextValue("product_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$discount_id = (array_key_exists('discount_id', $_GET)) ? $_GET['discount_id'] : null;
        
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
    'title' => "Product has discount",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_has_discount")
        ->addControl(SokoInputControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($product_id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.discount",
            ]))    
            ->setName("discount_id")
            ->setLabel("Discount id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("conditions")
            ->setLabel("Conditions")
            ->setType("textarea")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_product_has_discount"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $product_id, $discount_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product_has_discount", [
				"product_id" => $fData["product_id"],
				"discount_id" => $fData["discount_id"],
				"conditions" => $fData["conditions"],
				"active" => $fData["active"],

            ]);
            $form->addNotification("Le/la Product has discount pour le/la product \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product_has_discount", [
				"conditions" => $fData["conditions"],
				"active" => $fData["active"],

            ], [
				["product_id", "=", $product_id],
				["discount_id", "=", $discount_id],
            
            ]);
            $form->addNotification("Le/la Product has discount pour le/la product \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


