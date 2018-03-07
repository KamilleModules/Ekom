<?php


use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\TimezoneLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");


$shopLangs = ShopLayer::getLangIsoCodes($shopId);
$shopCurrencies = ShopLayer::getCurrencyIsoCodes($shopId);
$timezoneEntries = TimezoneLayer::getEntries();


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Shop",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-shop")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel('Id')
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shopId)
        )
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel('Label')
        )
        ->addControl(SokoInputControl::create()
            ->setName("host")
            ->setLabel('Host')
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel('Langue du front')
            ->setChoices($shopLangs)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("currency_id")
            ->setLabel('Devise du front')
            ->setChoices($shopCurrencies)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("base_currency_id")
            ->setLabel('Devise étalon')
            ->setChoices($shopCurrencies)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("timezone_id")
            ->setLabel('Fuseau horaire du front et back')
            ->setChoices($timezoneEntries)
        )
//        ->addControl(SokoBooleanChoiceControl::create()
//            ->setName("active")
//            ->setLabel('Actif')
//        )
//        ->addControl(SokoBooleanChoiceControl::create()
//            ->setName("is_base_currency")
//            ->setLabel('Transformer en monnaie de référence')
//            ->setValue(0)
//        )
//        ->addValidationRule("exchange_rate", SokoNotEmptyValidationRule::create())
    ,
    'feed' => function (SokoFormInterface $form) use ($shopId) {


        $q = "select 
id,
label, 
host,
lang_id,
currency_id,
base_currency_id,
timezone_id

from ek_shop
where id=$shopId

";
        $row = QuickPdo::fetch($q);
        if (false !== $row) {
            $form->inject($row);
        }
    },
    'process' => function ($fData, SokoFormInterface $form) use ($shopId) {


        ShopLayer::setBaseCurrency($shopId, $fData['base_currency_id']);
        QuickPdo::update("ek_shop", [
            "label" => $fData['label'],
            "host" => $fData['host'],
            "lang_id" => $fData['lang_id'],
            "currency_id" => $fData['currency_id'],
            "base_currency_id" => $fData['base_currency_id'],
        ], [
            ['id', '=', $shopId],
        ]);
        $form->addNotification("Les données du shop ont bien été mises à jour", "success");


        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'shop_id',
        'currency_id',
    ],
];