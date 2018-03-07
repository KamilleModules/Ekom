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


$choice_order_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from kamille.ek_order", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "Direct debit",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-pei_direct_debit")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("order_id")
            ->setLabel('Order id')
            ->setChoices($choice_order_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date")
            ->setLabel("Date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("paid")
            ->setLabel("Paid")
        )
        ->addControl(SokoInputControl::create()
            ->setName("transaction_reference")
            ->setLabel("Transaction_reference")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pay_id")
            ->setLabel("Pay_id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("feedback_details")
            ->setLabel("Feedback_details")
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("amount")
            ->setLabel("Amount")
        )
        ->addControl(SokoInputControl::create()
            ->setName("currency")
            ->setLabel("Currency")
        )
        ->addControl(SokoInputControl::create()
            ->setName("alias")
            ->setLabel("Alias")
        )
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop_id")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("pei_direct_debit"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("pei_direct_debit", [
				"order_id" => $fData["order_id"],
				"date" => $fData["date"],
				"paid" => $fData["paid"],
				"transaction_reference" => $fData["transaction_reference"],
				"pay_id" => $fData["pay_id"],
				"feedback_details" => $fData["feedback_details"],
				"amount" => $fData["amount"],
				"currency" => $fData["currency"],
				"alias" => $fData["alias"],
				"shop_id" => $fData["shop_id"],

            ]);
            $form->addNotification("Le/la Direct debit a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("pei_direct_debit", [
				"order_id" => $fData["order_id"],
				"date" => $fData["date"],
				"paid" => $fData["paid"],
				"transaction_reference" => $fData["transaction_reference"],
				"pay_id" => $fData["pay_id"],
				"feedback_details" => $fData["feedback_details"],
				"amount" => $fData["amount"],
				"currency" => $fData["currency"],
				"alias" => $fData["alias"],
				"shop_id" => $fData["shop_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Direct debit a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


