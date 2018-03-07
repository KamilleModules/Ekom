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
    'product_bundle_id',
    'product_id',
];
$product_bundle_id = MorphicHelper::getFormContextValue("product_bundle_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$product_id = (array_key_exists('product_id', $_GET)) ? $_GET['product_id'] : null;
        
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
    'title' => "Product bundle has product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_bundle_has_product")
        ->addControl(SokoInputControl::create()
            ->setName("product_bundle_id")
            ->setLabel("Product bundle id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($product_bundle_id)
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
    'feed' => MorphicHelper::getFeedFunction("ek_product_bundle_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $product_bundle_id, $product_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product_bundle_has_product", [
				"product_bundle_id" => $fData["product_bundle_id"],
				"product_id" => $fData["product_id"],
				"quantity" => $fData["quantity"],

            ]);
            $form->addNotification("Le/la Product bundle has product pour le/la product bundle \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product_bundle_has_product", [
				"quantity" => $fData["quantity"],

            ], [
				["product_bundle_id", "=", $product_bundle_id],
				["product_id", "=", $product_id],
            
            ]);
            $form->addNotification("Le/la Product bundle has product pour le/la product bundle \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


