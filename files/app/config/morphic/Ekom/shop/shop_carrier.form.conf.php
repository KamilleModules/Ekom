<?php


use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\PaymentLayer;
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
$carrierIdValue = (array_key_exists('carrier_id', $_GET)) ? $_GET['carrier_id'] : null;
$isUpdate = (null !== $shopIdValue && null !== $carrierIdValue);


//--------------------------------------------
// HYBRID CURRENCY CONTROL
//--------------------------------------------
$allItems = CarrierLayer::getAllCarriers();
$boundControl = SokoChoiceControl::create()
    ->setName("carrier_id")
    ->setLabel('Carrier id')
    ->setValue($carrierIdValue)
    ->setChoices($allItems);
if ($isUpdate) {
    $boundControl->setProperties([
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
    'title' => "Shop Carrier",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setAction("?" . http_build_query($_GET))
        ->setName("soko-form-shop_carrier")
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel('Shop id')
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($shopIdValue)
        )
        ->addControl($boundControl)
        ->addControl(SokoInputControl::create()
            ->setName("priority")
            ->setLabel('Priority')
            ->setValue(0)
        )
    ,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue) {


        if (null !== $shopIdValue) {

            $markers = [];

//        $values = array_intersect_key($_GET, array_flip($ric));
            $values = [];
            if (array_key_exists("carrier_id", $_GET)) {
                $values["h.carrier_id"] = (int)$_GET['carrier_id'];
            }


            $q = "select 
c.name,
h.shop_id

from ek_carrier c 
inner join ek_shop_has_carrier h on h.carrier_id=c.id 

where h.shop_id=$shopId

";
            QuickPdoStmtTool::addWhereEqualsSubStmt($values, $q, $markers);
            $row = QuickPdo::fetch("$q", $markers);
        } else {
            $row = ['shop_id' => $shopId];
        }
        $form->inject($row);
    },
    'process' => function ($fData, SokoFormInterface $form) use ($shopIdValue, $carrierIdValue, $isUpdate) {

        if (true === $isUpdate) { // update
            QuickPdo::update("ek_shop_has_carrier", [
                "priority" => $fData['priority'],
            ], [
                ['shop_id', '=', $shopIdValue],
                ['carrier_id', '=', $carrierIdValue],
            ]);
            $form->addNotification("Le transporteur a bien été mis à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_shop_has_carrier", [
                    "shop_id" => $fData['shop_id'],
                    "carrier_id" => $fData['carrier_id'],
                    "priority" => $fData['priority'],
                ]);
                $form->addNotification("Le transporteur a bien été ajouté", "success");
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
        'carrier_id',
    ],
];