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
$choice_seller_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_seller", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "Invoice",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_invoice")
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
                'action' => "auto.user",
            ]))    
            ->setName("user_id")
            ->setLabel("User id")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("order_id")
            ->setLabel('Order id')
            ->setChoices($choice_order_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("seller_id")
            ->setLabel('Seller id')
            ->setChoices($choice_seller_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel("Label")
        )
        ->addControl(SokoInputControl::create()
            ->setName("invoice_number")
            ->setLabel("Invoice_number")
        )
        ->addControl(SokoInputControl::create()
            ->setName("invoice_number_alt")
            ->setLabel("Invoice_number_alt")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("invoice_date")
            ->setLabel("Invoice_date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("payment_method")
            ->setLabel("Payment_method")
        )
        ->addControl(SokoInputControl::create()
            ->setName("currency_iso_code")
            ->setLabel("Currency_iso_code")
        )
        ->addControl(SokoInputControl::create()
            ->setName("lang_iso_code")
            ->setLabel("Lang_iso_code")
        )
        ->addControl(SokoInputControl::create()
            ->setName("shop_host")
            ->setLabel("Shop_host")
        )
        ->addControl(SokoInputControl::create()
            ->setName("track_identifier")
            ->setLabel("Track_identifier")
        )
        ->addControl(SokoInputControl::create()
            ->setName("amount")
            ->setLabel("Amount")
        )
        ->addControl(SokoInputControl::create()
            ->setName("seller")
            ->setLabel("Seller")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_invoice"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_invoice", [
				"shop_id" => $fData["shop_id"],
				"user_id" => $fData["user_id"],
				"order_id" => $fData["order_id"],
				"seller_id" => $fData["seller_id"],
				"label" => $fData["label"],
				"invoice_number" => $fData["invoice_number"],
				"invoice_number_alt" => $fData["invoice_number_alt"],
				"invoice_date" => $fData["invoice_date"],
				"payment_method" => $fData["payment_method"],
				"currency_iso_code" => $fData["currency_iso_code"],
				"lang_iso_code" => $fData["lang_iso_code"],
				"shop_host" => $fData["shop_host"],
				"track_identifier" => $fData["track_identifier"],
				"amount" => $fData["amount"],
				"seller" => $fData["seller"],
				"user_info" => $fData["user_info"],
				"seller_address" => $fData["seller_address"],
				"shipping_address" => $fData["shipping_address"],
				"billing_address" => $fData["billing_address"],
				"invoice_details" => $fData["invoice_details"],

            ]);
            $form->addNotification("Le/la Invoice a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_invoice", [
				"shop_id" => $fData["shop_id"],
				"user_id" => $fData["user_id"],
				"order_id" => $fData["order_id"],
				"seller_id" => $fData["seller_id"],
				"label" => $fData["label"],
				"invoice_number" => $fData["invoice_number"],
				"invoice_number_alt" => $fData["invoice_number_alt"],
				"invoice_date" => $fData["invoice_date"],
				"payment_method" => $fData["payment_method"],
				"currency_iso_code" => $fData["currency_iso_code"],
				"lang_iso_code" => $fData["lang_iso_code"],
				"shop_host" => $fData["shop_host"],
				"track_identifier" => $fData["track_identifier"],
				"amount" => $fData["amount"],
				"seller" => $fData["seller"],
				"user_info" => $fData["user_info"],
				"seller_address" => $fData["seller_address"],
				"shipping_address" => $fData["shipping_address"],
				"billing_address" => $fData["billing_address"],
				"invoice_details" => $fData["invoice_details"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Invoice a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


