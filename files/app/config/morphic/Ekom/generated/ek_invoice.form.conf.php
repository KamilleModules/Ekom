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

// inferred data (can be overridden by fkeys)
$shop_id = EkomNullosUser::getEkomValue("shop_id");
$lang_id = EkomNullosUser::getEkomValue("lang_id");
$currency_id = EkomNullosUser::getEkomValue("currency_id");

$choice_order_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_order", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_seller_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_seller", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_user_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from ek_user", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$order_id = (array_key_exists("order_id", $_GET)) ? $_GET['order_id'] : null;
$seller_id = (array_key_exists("seller_id", $_GET)) ? $_GET['seller_id'] : null;
$shop_id = (array_key_exists("shop_id", $_GET)) ? $_GET['shop_id'] : $shop_id; // inferred
$user_id = (array_key_exists("user_id", $_GET)) ? $_GET['user_id'] : null;



$avatar = (array_key_exists("avatar", $context)) ? $context['avatar'] : null;

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
    'title' => "invoice",
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
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => (null !== $user_id),
            ])
            ->setValue($user_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.user",
            ]))         )
        ->addControl(SokoChoiceControl::create()
            ->setName("order_id")
            ->setLabel("Order id")
            ->setProperties([
                'readonly' => (null !== $order_id),
            ])
            ->setValue($order_id)
            ->setChoices($choice_order_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("seller_id")
            ->setLabel("Seller id")
            ->setProperties([
                'readonly' => (null !== $seller_id),
            ])
            ->setValue($seller_id)
            ->setChoices($choice_seller_id))
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
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_invoice"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_invoice", [
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

            ], '', $ric);
            $form->addNotification("Le/la invoice a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
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
            $form->addNotification("Le/la invoice a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,            
    //--------------------------------------------
    // CHILDREN
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkPayment_List") . "?s&invoice_id=$id",
                    "text" => "Voir les payments",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
