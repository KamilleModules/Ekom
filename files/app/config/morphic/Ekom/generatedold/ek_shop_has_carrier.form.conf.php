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


$choice_carrier_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_carrier", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'carrier_id',
];
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$carrier_id = (array_key_exists('carrier_id', $_GET)) ? $_GET['carrier_id'] : null;
        
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
    'title' => "Shop has carrier",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_carrier")
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shop_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("carrier_id")
            ->setLabel('Carrier id')
            ->setChoices($choice_carrier_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($carrier_id)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("priority")
            ->setLabel("Priority")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_carrier"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $carrier_id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_has_carrier", [
				"shop_id" => $fData["shop_id"],
				"carrier_id" => $fData["carrier_id"],
				"priority" => $fData["priority"],

            ]);
            $form->addNotification("Le/la Shop has carrier pour le/la shop \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_has_carrier", [
				"shop_id" => $fData["shop_id"],
				"priority" => $fData["priority"],

            ], [
				["carrier_id", "=", $carrier_id],
            
            ]);
            $form->addNotification("Le/la Shop has carrier pour le/la shop \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


