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


$choice_provider_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_provider", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric = [
    'shop_id',
    'product_id',
    'provider_id',
];

$shop_id = (array_key_exists("shop_id", $_GET)) ? $_GET['shop_id'] : $shop_id; // that's how inferred data is inferred
$product_id = (array_key_exists('product_id', $_GET)) ? $_GET['product_id'] : null;
$provider_id = (array_key_exists('provider_id', $_GET)) ? $_GET['provider_id'] : null;




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
    'title' => "Shop has product has provider",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_product_has_provider")
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
        )
        ->addControl(SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => (null !== $product_id),
            ])
            ->setValue($product_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("provider_id")
            ->setLabel('Provider id')
            ->setChoices($choice_provider_id)
            ->setProperties([
                'readonly' => (null !== $provider_id),
            ])
            ->setValue($provider_id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("wholesale_price")
            ->setLabel("Wholesale_price")
        )
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product_has_provider"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $shop_id, $product_id, $provider_id) {


        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_has_product_has_provider", [
                "shop_id" => $fData["shop_id"],
                "product_id" => $fData["product_id"],
                "provider_id" => $fData["provider_id"],
                "wholesale_price" => $fData["wholesale_price"],

            ]);
            $form->addNotification("Le/la Shop has product has provider pour le/la shop \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_has_product_has_provider", [
                "shop_id" => $fData["shop_id"],
                "wholesale_price" => $fData["wholesale_price"],

            ], [
                ["shop_id", "=", $shop_id],
                ["product_id", "=", $product_id],
                ["provider_id", "=", $provider_id],

            ]);
            $form->addNotification("Le/la Shop has product has provider pour le/la shop \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


