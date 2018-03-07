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
];
        
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
    'title' => "Shop configuration",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_configuration")
        ->addControl(SokoInputControl::create()
            ->setName("key")
            ->setLabel("Key")
        )
        ->addControl(SokoInputControl::create()
            ->setName("value")
            ->setLabel("Value")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_shop_configuration"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_configuration", [
				"shop_id" => $fData["shop_id"],
				"key" => $fData["key"],
				"value" => $fData["value"],

            ]);
            $form->addNotification("Le/la Shop configuration a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_configuration", [
				"shop_id" => $fData["shop_id"],
				"key" => $fData["key"],
				"value" => $fData["value"],

            ], [
            
            ]);
            $form->addNotification("Le/la Shop configuration a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


