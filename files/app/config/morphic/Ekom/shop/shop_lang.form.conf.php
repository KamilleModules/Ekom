<?php


use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\LangLayer;
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
$langIdValue = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;
$isUpdate = (null !== $shopIdValue && null !== $langIdValue);


//--------------------------------------------
// HYBRID CURRENCY CONTROL
//--------------------------------------------
$allItems = LangLayer::getLangItems();
$langControl = SokoChoiceControl::create()
    ->setName("lang_id")
    ->setLabel('Lang id')
    ->setValue($langIdValue)
    ->setChoices($allItems);
if ($isUpdate) {
    $langControl->setProperties([
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
    'title' => "Shop Lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setAction("?" . http_build_query($_GET))
        ->setName("soko-form-shop_lang")
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel('Shop id')
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shopIdValue)
        )
        ->addControl($langControl)
    ,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue) {


        if (null !== $shopIdValue) {

            $markers = [];

//        $values = array_intersect_key($_GET, array_flip($ric));
            $values = [];
            if (array_key_exists("lang_id", $_GET)) {
                $values["h.lang_id"] = (int)$_GET['lang_id'];
            }


            $q = "select 
l.iso_code,
h.shop_id

from ek_lang l 
inner join ek_shop_has_lang h on h.lang_id=l.id 

where h.shop_id=$shopId

";
            QuickPdoStmtTool::addWhereEqualsSubStmt($values, $q, $markers);
            $row = QuickPdo::fetch("$q", $markers);
        } else {
            $row = ['shop_id' => $shopId];
        }
        $form->inject($row);
    },
    'process' => function ($fData, SokoFormInterface $form) use ($shopIdValue, $langIdValue, $isUpdate) {

        if (true === $isUpdate) { // update

            /**
             * Since there are no non primary key fields in this table,
             * we don't need to update anything.
             */
//            QuickPdo::update("ek_shop_has_lang", [
//                "exchange_rate" => $fData['exchange_rate'],
//                "active" => $fData['active'],
//            ], [
//                ['shop_id', '=', $shopIdValue],
//                ['lang_id', '=', $langIdValue],
//            ]);
            $form->addNotification("La langue a bien été mise à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_shop_has_lang", [
                    "shop_id" => $fData['shop_id'],
                    "lang_id" => $fData['lang_id'],
                ]);
                $form->addNotification("La langue a bien été ajoutée", "success");
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
        'lang_id',
    ],
];