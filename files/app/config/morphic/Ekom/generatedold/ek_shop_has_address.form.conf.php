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
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);
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
    'title' => "Shop has address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_address")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shop_id)
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
            ->setName("type")
            ->setLabel("Type")
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_has_address", [
				"shop_id" => $fData["shop_id"],
				"address_id" => $fData["address_id"],
				"type" => $fData["type"],
				"order" => $fData["order"],

            ]);
            $form->addNotification("Le/la Shop has address pour le/la shop \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_has_address", [
				"shop_id" => $fData["shop_id"],
				"address_id" => $fData["address_id"],
				"type" => $fData["type"],
				"order" => $fData["order"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Shop has address pour le/la shop \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


