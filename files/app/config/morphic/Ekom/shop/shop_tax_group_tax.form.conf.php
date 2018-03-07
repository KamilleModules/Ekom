<?php


use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Exception\EkomException;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

if (
    array_key_exists("tax_group_id", $context) &&
    array_key_exists("tax_group_label", $context)
) {

    $tax_group_id = $context['tax_group_id'];
    $tax_group_label = $context['tax_group_label'];

    $taxId = (array_key_exists("tax_id", $_GET)) ? (int)$_GET['tax_id'] : 0;


    $isUpdate = (0 !== $taxId);


    //--------------------------------------------
    // HYBRID CURRENCY CONTROL
    //--------------------------------------------
//    $taxControl = SokoChoiceControl::create()
//        ->setName("tax_id")
//        ->setLabel('Address id')
//        ->setValue($taxId);
//    $allAddresses = AddressLayer::getEntries();
//    $taxControl
//        ->setChoices($allAddresses);



    $taxItems = TaxLayer::getTaxItems();
    $taxControl = SokoChoiceControl::create()
        ->setChoices($taxItems)
        ->setName("tax_id")
        ->setLabel('Tax id')
        ->setValue($taxId);


    if ($isUpdate) {
        $taxControl->setProperties([
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
        'title' => "Shop tax group \"" . $tax_group_label . "\" tax",
        //--------------------------------------------
        // SOKO FORM
        'form' => SokoForm::create()
            ->setAction("?" . http_build_query($_GET))
            ->setName("soko-form-shop_tax_group_tax")
            ->addControl(SokoInputControl::create()
                ->setName("tax_group_id")
                ->setLabel('Tax group id')
                ->setProperties([
                    'readonly' => true,
                ])
                ->setValue($tax_group_id)
            )
            ->addControl($taxControl)
            ->addControl(SokoInputControl::create()
                ->setName("order")
                ->setLabel('Order')
                ->setValue(0)
            )
            ->addControl(SokoChoiceControl::create()
                ->setName("mode")
                ->setLabel('Mode')
                ->setChoices(TaxLayer::getModeItems())
            )
//            ->addValidationRule("exchange_rate", SokoNotEmptyValidationRule::create())
        ,
        'feed' => function (SokoFormInterface $form, array $ric) use ($tax_group_id, $taxId) {
            if (null !== $tax_group_id) {
                $markers = [];



                $q = "select *
from ek_tax_group_has_tax 
where tax_group_id=$tax_group_id
and tax_id=$taxId

";
                $row = QuickPdo::fetch("$q", $markers);
            } else {
                $row = ['tax_group_id' => $tax_group_id];

            }
            $form->inject($row);
        },
        'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $tax_group_id, $taxId) {

            if (true === $isUpdate) { // update
                QuickPdo::update("ek_tax_group_has_tax", [
                    "order" => $fData['order'],
                    "mode" => $fData['mode'],
                ], [
                    ['tax_group_id', '=', $tax_group_id],
                    ['tax_id', '=', $taxId],
                ]);
                $form->addNotification("La taxe a bien été mise à jour", "success");
            } else {

                try {

                    QuickPdo::insert("ek_tax_group_has_tax", [
                        "tax_group_id" => $fData['tax_group_id'],
                        "tax_id" => $fData['tax_id'],
                        "order" => $fData['order'],
                        "mode" => $fData['mode'],
                    ]);
                    $form->addNotification("La taxe a bien été ajoutée", "success");
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
            'tax_group_id',
            'tax_id',
        ],
    ];


} else {
    throw new EkomException("Some variables not found in the given context");
}