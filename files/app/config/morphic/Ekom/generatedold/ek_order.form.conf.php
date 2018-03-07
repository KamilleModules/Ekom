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
    'title' => "Order",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_order")
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
        ->addControl(SokoInputControl::create()
            ->setName("reference")
            ->setLabel("Reference")
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
            ->setName("amount")
            ->setLabel("Amount")
        )
        ->addControl(SokoInputControl::create()
            ->setName("coupon_saving")
            ->setLabel("Coupon_saving")
        )
        ->addControl(SokoInputControl::create()
            ->setName("cart_quantity")
            ->setLabel("Cart_quantity")
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
            ->setName("payment_method")
            ->setLabel("Payment_method")
        )
        ->addControl(SokoInputControl::create()
            ->setName("payment_method_extra")
            ->setLabel("Payment_method_extra")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_order"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_order", [
				"shop_id" => $fData["shop_id"],
				"user_id" => $fData["user_id"],
				"reference" => $fData["reference"],
				"date" => $fData["date"],
				"amount" => $fData["amount"],
				"coupon_saving" => $fData["coupon_saving"],
				"cart_quantity" => $fData["cart_quantity"],
				"currency_iso_code" => $fData["currency_iso_code"],
				"lang_iso_code" => $fData["lang_iso_code"],
				"payment_method" => $fData["payment_method"],
				"payment_method_extra" => $fData["payment_method_extra"],
				"user_info" => $fData["user_info"],
				"shop_info" => $fData["shop_info"],
				"shipping_address" => $fData["shipping_address"],
				"billing_address" => $fData["billing_address"],
				"order_details" => $fData["order_details"],

            ]);
            $form->addNotification("Le/la Order a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_order", [
				"shop_id" => $fData["shop_id"],
				"user_id" => $fData["user_id"],
				"reference" => $fData["reference"],
				"date" => $fData["date"],
				"amount" => $fData["amount"],
				"coupon_saving" => $fData["coupon_saving"],
				"cart_quantity" => $fData["cart_quantity"],
				"currency_iso_code" => $fData["currency_iso_code"],
				"lang_iso_code" => $fData["lang_iso_code"],
				"payment_method" => $fData["payment_method"],
				"payment_method_extra" => $fData["payment_method_extra"],
				"user_info" => $fData["user_info"],
				"shop_info" => $fData["shop_info"],
				"shipping_address" => $fData["shipping_address"],
				"billing_address" => $fData["billing_address"],
				"order_details" => $fData["order_details"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Order a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
    //--------------------------------------------
    // IF HAS CONTEXT
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkOrderHasOrderStatus_List") . "?order_id=$id",
                    "text" => "Voir les order statuses de ce/cette Order",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


