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
    'title' => "Card",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_card")
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
                'action' => "auto.product_card",
            ]))    
            ->setName("product_card_id")
            ->setLabel("Product card id")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ektra_card"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ektra_card", [
				"product_card_id" => $fData["product_card_id"],
				"shop_id" => $fData["shop_id"],

            ]);
            $form->addNotification("Le/la Card a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ektra_card", [
				"product_card_id" => $fData["product_card_id"],
				"shop_id" => $fData["shop_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Card a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


