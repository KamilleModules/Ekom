<?php


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
$paymentMethodIdValue = (array_key_exists('payment_method_id', $_GET)) ? $_GET['payment_method_id'] : null;
$isUpdate = (null !== $shopIdValue && null !== $paymentMethodIdValue);


//--------------------------------------------
// HYBRID CURRENCY CONTROL
//--------------------------------------------
$allItems = PaymentLayer::getPaymentMethodItems();
$boundControl = SokoChoiceControl::create()
    ->setName("payment_method_id")
    ->setLabel('Payment method id')
    ->setValue($paymentMethodIdValue)
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
    'title' => "Shop Payment method",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setAction("?" . http_build_query($_GET))
        ->setName("soko-form-shop_payment_method")
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
            ->setName("order")
            ->setLabel('Order')
            ->setValue(0)
        )
        ->addControl(SokoInputControl::create()
            ->setName("configuration")
            ->setLabel('Configuration')
            ->setType("textarea")
        )
    ,
    'feed' => function (SokoFormInterface $form, array $ric) use ($shopId, $shopIdValue) {


        if (null !== $shopIdValue) {

            $markers = [];

//        $values = array_intersect_key($_GET, array_flip($ric));
            $values = [];
            if (array_key_exists("payment_method_id", $_GET)) {
                $values["h.payment_method_id"] = (int)$_GET['payment_method_id'];
            }


            $q = "select 
p.name,
h.shop_id

from ek_payment_method p 
inner join ek_shop_has_payment_method h on h.payment_method_id=p.id 

where h.shop_id=$shopId

";
            QuickPdoStmtTool::addWhereEqualsSubStmt($values, $q, $markers);
            $row = QuickPdo::fetch("$q", $markers);
        } else {
            $row = ['shop_id' => $shopId];
        }
        $form->inject($row);
    },
    'process' => function ($fData, SokoFormInterface $form) use ($shopIdValue, $paymentMethodIdValue, $isUpdate) {

        if (true === $isUpdate) { // update
            QuickPdo::update("ek_shop_has_payment_method", [
                "order" => $fData['order'],
                "configuration" => $fData['configuration'],
            ], [
                ['shop_id', '=', $shopIdValue],
                ['payment_method_id', '=', $paymentMethodIdValue],
            ]);
            $form->addNotification("La méthode de paiement a bien été mise à jour", "success");
        } else {

            try {

                QuickPdo::insert("ek_shop_has_payment_method", [
                    "shop_id" => $fData['shop_id'],
                    "payment_method_id" => $fData['payment_method_id'],
                    "order" => $fData['order'],
                    "configuration" => $fData['configuration'],
                ]);
                $form->addNotification("La méthode de paiement a bien été ajoutée", "success");
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
        'payment_method_id',
    ],
];