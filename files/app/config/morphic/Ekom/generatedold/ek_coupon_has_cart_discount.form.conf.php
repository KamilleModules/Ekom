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


$choice_cart_discount_id = QuickPdo::fetchAll("select id, concat(id, \". \", target) as label from kamille.ek_cart_discount", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'coupon_id',
    'cart_discount_id',
];
$coupon_id = MorphicHelper::getFormContextValue("coupon_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$cart_discount_id = (array_key_exists('cart_discount_id', $_GET)) ? $_GET['cart_discount_id'] : null;
        
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
    'title' => "Coupon has cart discount",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_coupon_has_cart_discount")
        ->addControl(SokoInputControl::create()
            ->setName("coupon_id")
            ->setLabel("Coupon id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($coupon_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("cart_discount_id")
            ->setLabel('Cart discount id')
            ->setChoices($choice_cart_discount_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($cart_discount_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_coupon_has_cart_discount"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $coupon_id, $cart_discount_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_coupon_has_cart_discount", [
				"coupon_id" => $fData["coupon_id"],
				"cart_discount_id" => $fData["cart_discount_id"],

            ]);
            $form->addNotification("Le/la Coupon has cart discount pour le/la coupon \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_coupon_has_cart_discount", [

            ], [
				["coupon_id", "=", $coupon_id],
				["cart_discount_id", "=", $cart_discount_id],
            
            ]);
            $form->addNotification("Le/la Coupon has cart discount pour le/la coupon \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


