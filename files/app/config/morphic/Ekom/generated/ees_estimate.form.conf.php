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

$choice_user_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from ek_user", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
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
    'title' => "estimate",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ees_estimate")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => (null !== $user_id),
            ])
            ->setValue($user_id)
            ->setChoices($choice_user_id))
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
            ->setName("shop_info")
            ->setLabel("Shop_info")
            ->setType("textarea")
        ),
    'feed' => MorphicHelper::getFeedFunction("ees_estimate"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ees_estimate", [
				"user_id" => $fData["user_id"],
				"reference" => $fData["reference"],
				"date" => $fData["date"],
				"amount" => $fData["amount"],
				"coupon_saving" => $fData["coupon_saving"],
				"cart_quantity" => $fData["cart_quantity"],
				"currency_iso_code" => $fData["currency_iso_code"],
				"lang_iso_code" => $fData["lang_iso_code"],
				"user_info" => $fData["user_info"],
				"shop_info" => $fData["shop_info"],
				"shipping_address" => $fData["shipping_address"],
				"billing_address" => $fData["billing_address"],
				"order_details" => $fData["order_details"],

            ], '', $ric);
            $form->addNotification("Le/la estimate a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ees_estimate", [
				"user_id" => $fData["user_id"],
				"reference" => $fData["reference"],
				"date" => $fData["date"],
				"amount" => $fData["amount"],
				"coupon_saving" => $fData["coupon_saving"],
				"cart_quantity" => $fData["cart_quantity"],
				"currency_iso_code" => $fData["currency_iso_code"],
				"lang_iso_code" => $fData["lang_iso_code"],
				"user_info" => $fData["user_info"],
				"shop_info" => $fData["shop_info"],
				"shipping_address" => $fData["shipping_address"],
				"billing_address" => $fData["billing_address"],
				"order_details" => $fData["order_details"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la estimate a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
