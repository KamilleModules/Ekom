<?php


use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\Exception\QuickPdoException;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");


$shopIdValue = (array_key_exists('shop_id', $_GET)) ? $_GET['shop_id'] : $shopId;
$currencyIdValue = (array_key_exists('currency_id', $_GET)) ? $_GET['currency_id'] : null;
$isUpdate = (null !== $shopIdValue && null !== $currencyIdValue);


//--------------------------------------------
// HYBRID CURRENCY CONTROL
//--------------------------------------------
$allCurrencies = CurrencyLayer::getCurrencyItems();
$currencyControl = SokoChoiceControl::create()
    ->setName("currency_id")
    ->setLabel('Currency id')
    ->setValue($currencyIdValue)
    ->setChoices($allCurrencies);
if ($isUpdate) {
    $currencyControl->setProperties([
        'readonly' => true,
    ]);
}


//--------------------------------------------
// CONF
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Shop Currency",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setAction("?" . http_build_query($_GET))
        ->setName("soko-form-shop_currency")
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel('Shop id')
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shopIdValue)
        )
        ->addControl($currencyControl)
        ->addControl(SokoInputControl::create()
            ->setName("exchange_rate")
            ->setLabel('Taux d\'échange')
            ->setProperties([
                'required' => false,
            ])
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel('Actif')
            ->setValue(1)
        )
        ->addValidationRule("exchange_rate", SokoNotEmptyValidationRule::create())
    ,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue) {


        if (null !== $shopIdValue) {

            $markers = [];

//        $values = array_intersect_key($_GET, array_flip($ric));
            $values = [];
            if (array_key_exists("currency_id", $_GET)) {
                $values["h.currency_id"] = (int)$_GET['currency_id'];
            }


            $q = "select 
c.iso_code,
h.shop_id,
h.currency_id,
h.exchange_rate,
h.active

from ek_currency c 
inner join ek_shop_has_currency h on h.currency_id=c.id 
inner join ek_shop s on s.id=h.shop_id

where h.shop_id=$shopId

";
            QuickPdoStmtTool::addWhereEqualsSubStmt($values, $q, $markers);
            $row = QuickPdo::fetch("$q", $markers);
        } else {
            $row = ['shop_id' => $shopId];

        }
        $form->inject($row);
    },
    'process' => function ($fData, SokoFormInterface $form) use ($shopIdValue, $currencyIdValue, $isUpdate) {

        if (true === $isUpdate) { // update
            QuickPdo::update("ek_shop_has_currency", [
                "exchange_rate" => $fData['exchange_rate'],
                "active" => $fData['active'],
            ], [
                ['shop_id', '=', $shopIdValue],
                ['currency_id', '=', $currencyIdValue],
            ]);
            $form->addNotification("La devise a bien été mise à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_shop_has_currency", [
                    "shop_id" => $fData['shop_id'],
                    "currency_id" => $fData['currency_id'],
                    "exchange_rate" => $fData['exchange_rate'],
                    "active" => $fData['active'],
                ]);
                $form->addNotification("La devise a bien été ajoutée", "success");
            } catch (\PDOException $e) {
                if (QuickPdoExceptionTool::isDuplicateEntry($e)) {
                    $form->addNotification("Cette entrée existe déjà", "warning");
                } else {
                    throw $e;
                }
            }
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'shop_id',
        'currency_id',
    ],
];