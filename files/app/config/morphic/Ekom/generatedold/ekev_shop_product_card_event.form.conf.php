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


$choice_event_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ekev_event", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'event_id',
    'product_card_id',
];
$event_id = (array_key_exists('event_id', $_GET)) ? $_GET['event_id'] : null;
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
    'title' => "Shop product card event",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_shop_product_card_event")
        ->addControl(SokoChoiceControl::create()
            ->setName("event_id")
            ->setLabel('Event id')
            ->setChoices($choice_event_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($event_id)
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
    'feed' => MorphicHelper::getFeedFunction("ekev_shop_product_card_event"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $event_id, $product_card_id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_shop_product_card_event", [
				"shop_id" => $fData["shop_id"],
				"event_id" => $fData["event_id"],
				"product_card_id" => $fData["product_card_id"],

            ]);
            $form->addNotification("Le/la Shop product card event a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_shop_product_card_event", [
				"shop_id" => $fData["shop_id"],

            ], [
				["event_id", "=", $event_id],
				["product_card_id", "=", $product_card_id],
            
            ]);
            $form->addNotification("Le/la Shop product card event a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


