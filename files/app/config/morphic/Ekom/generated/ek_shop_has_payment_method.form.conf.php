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

$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_payment_method_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_payment_method", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'shop_id',
    'payment_method_id',
];

$shop_id = (array_key_exists("shop_id", $_GET)) ? $_GET['shop_id'] : $shop_id; // inferred
$payment_method_id = (array_key_exists("payment_method_id", $_GET)) ? $_GET['payment_method_id'] : null;



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
    'title' => "shop-payment method",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_payment_method")
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("payment_method_id")
            ->setLabel("Payment method id")
            ->setProperties([
                'readonly' => (null !== $payment_method_id),
            ])
            ->setValue($payment_method_id)
            ->setChoices($choice_payment_method_id))
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        )
        ->addControl(SokoInputControl::create()
            ->setName("configuration")
            ->setLabel("Configuration")
            ->setType("textarea")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_payment_method"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $shop_id, $payment_method_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_shop_has_payment_method", [
				"shop_id" => $fData["shop_id"],
				"payment_method_id" => $fData["payment_method_id"],
				"order" => $fData["order"],
				"configuration" => $fData["configuration"],

            ], '', $ric);
            $form->addNotification("Le/la shop-payment method a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_shop_has_payment_method", [
				"order" => $fData["order"],
				"configuration" => $fData["configuration"],

            ], [
				["shop_id", "=", $shop_id],
				["payment_method_id", "=", $payment_method_id],
            
            ]);
            $form->addNotification("Le/la shop-payment method a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
