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


$choice_currency_id = QuickPdo::fetchAll("select id, concat(id, \". \", iso_code) as label from kamille.ek_currency", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'currency_id',
];
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$currency_id = (array_key_exists('currency_id', $_GET)) ? $_GET['currency_id'] : null;
        
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
    'title' => "Shop has currency",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_currency")
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shop_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("currency_id")
            ->setLabel('Currency id')
            ->setChoices($choice_currency_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($currency_id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("exchange_rate")
            ->setLabel("Exchange_rate")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_currency"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $currency_id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_has_currency", [
				"shop_id" => $fData["shop_id"],
				"currency_id" => $fData["currency_id"],
				"exchange_rate" => $fData["exchange_rate"],
				"active" => $fData["active"],

            ]);
            $form->addNotification("Le/la Shop has currency pour le/la shop \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_has_currency", [
				"shop_id" => $fData["shop_id"],
				"exchange_rate" => $fData["exchange_rate"],
				"active" => $fData["active"],

            ], [
				["currency_id", "=", $currency_id],
            
            ]);
            $form->addNotification("Le/la Shop has currency pour le/la shop \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


