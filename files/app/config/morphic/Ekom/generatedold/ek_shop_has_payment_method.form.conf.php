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


$choice_payment_method_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_payment_method", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'payment_method_id',
];
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$payment_method_id = (array_key_exists('payment_method_id', $_GET)) ? $_GET['payment_method_id'] : null;
        
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
    'title' => "Shop has payment method",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_payment_method")
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shop_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("payment_method_id")
            ->setLabel('Payment method id')
            ->setChoices($choice_payment_method_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($payment_method_id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        )
        ->addControl(SokoInputControl::create()
            ->setName("configuration")
            ->setLabel("Configuration")
            ->setType("textarea")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_payment_method"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $payment_method_id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_has_payment_method", [
				"shop_id" => $fData["shop_id"],
				"payment_method_id" => $fData["payment_method_id"],
				"order" => $fData["order"],
				"configuration" => $fData["configuration"],

            ]);
            $form->addNotification("Le/la Shop has payment method pour le/la shop \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_has_payment_method", [
				"shop_id" => $fData["shop_id"],
				"order" => $fData["order"],
				"configuration" => $fData["configuration"],

            ], [
				["payment_method_id", "=", $payment_method_id],
            
            ]);
            $form->addNotification("Le/la Shop has payment method pour le/la shop \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


