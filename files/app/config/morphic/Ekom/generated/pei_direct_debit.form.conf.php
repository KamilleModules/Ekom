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



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$order_id = (array_key_exists("order_id", $_GET)) ? $_GET['order_id'] : null;



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
    'title' => "direct debit",
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
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("order_id")
            ->setLabel("Order id")
            ->setProperties([
                'readonly' => (null !== $order_id),
            ])
            ->setValue($order_id)
            ->setChoices($choice_order_id))
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
        ),
    'feed' => MorphicHelper::getFeedFunction("pei_direct_debit"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("pei_direct_debit", [
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

            ], '', $ric);
            $form->addNotification("Le/la direct debit a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
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
            $form->addNotification("Le/la direct debit a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
