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

$choice_cart_discount_id = QuickPdo::fetchAll("select id, concat(id, \". \", target) as label from ek_cart_discount", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_coupon_id = QuickPdo::fetchAll("select id, concat(id, \". \", code) as label from ek_coupon", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'coupon_id',
    'cart_discount_id',
];

$cart_discount_id = (array_key_exists("cart_discount_id", $_GET)) ? $_GET['cart_discount_id'] : null;
$coupon_id = (array_key_exists("coupon_id", $_GET)) ? $_GET['coupon_id'] : null;



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
    'title' => "coupon-cart discount",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_coupon_has_cart_discount")
        ->addControl(SokoChoiceControl::create()
            ->setName("coupon_id")
            ->setLabel("Coupon id")
            ->setProperties([
                'readonly' => (null !== $coupon_id),
            ])
            ->setValue($coupon_id)
            ->setChoices($choice_coupon_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("cart_discount_id")
            ->setLabel("Cart discount id")
            ->setProperties([
                'readonly' => (null !== $cart_discount_id),
            ])
            ->setValue($cart_discount_id)
            ->setChoices($choice_cart_discount_id)),
    'feed' => MorphicHelper::getFeedFunction("ek_coupon_has_cart_discount"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $coupon_id, $cart_discount_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_coupon_has_cart_discount", [
				"coupon_id" => $fData["coupon_id"],
				"cart_discount_id" => $fData["cart_discount_id"],

            ], '', $ric);
            $form->addNotification("Le/la coupon-cart discount a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_coupon_has_cart_discount", [

            ], [
				["coupon_id", "=", $coupon_id],
				["cart_discount_id", "=", $cart_discount_id],
            
            ]);
            $form->addNotification("Le/la coupon-cart discount a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
