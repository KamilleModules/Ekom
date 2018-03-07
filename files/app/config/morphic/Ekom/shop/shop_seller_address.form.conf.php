<?php


use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\Exception\EkomException;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;

if (
    array_key_exists("seller_id", $context) &&
    array_key_exists("seller_name", $context)
) {

    $seller_id = $context['seller_id'];
    $seller_name = $context['seller_name'];

    $addressId = (array_key_exists("address_id", $_GET)) ? (int)$_GET['address_id'] : 0;


    $isUpdate = (0 !== $addressId);


    //--------------------------------------------
    // HYBRID CURRENCY CONTROL
    //--------------------------------------------
//    $addressControl = SokoChoiceControl::create()
//        ->setName("address_id")
//        ->setLabel('Address id')
//        ->setValue($addressId);
//    $allAddresses = AddressLayer::getEntries();
//    $addressControl
//        ->setChoices($allAddresses);


    $addressControl = SokoAutocompleteInputControl::create()
        ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
            'action' => "auto.address",
        ]))
        ->setName("address_id")
        ->setLabel('Address id')
        ->setValue($addressId);


    if ($isUpdate) {
        $addressControl->setProperties([
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
        'title' => "Shop Seller \"" . $seller_name . "\" address",
        //--------------------------------------------
        // SOKO FORM
        'form' => SokoForm::create()
            ->setAction("?" . http_build_query($_GET))
            ->setName("soko-form-shop_seller_address")
            ->addControl(SokoInputControl::create()
                ->setName("seller_id")
                ->setLabel('Seller id')
                ->setProperties([
                    'readonly' => true,
                ])
                ->setValue($seller_id)
            )
            ->addControl($addressControl)
            ->addControl(SokoInputControl::create()
                ->setName("order")
                ->setLabel('Order')
                ->setValue(0)
            )
//            ->addValidationRule("exchange_rate", SokoNotEmptyValidationRule::create())
        ,
        'feed' => function (SokoFormInterface $form, array $ric) use ($seller_id, $addressId) {
            if (null !== $seller_id) {
                $markers = [];

//        $values = array_intersect_key($_GET, array_flip($ric));
                $values = [];
                if (array_key_exists("currency_id", $_GET)) {
                    $values["h.currency_id"] = (int)$_GET['currency_id'];
                }


                $q = "select 
seller_id,
address_id,
`order`

from ek_seller_has_address 
where seller_id=$seller_id
and address_id=$addressId

";
                $row = QuickPdo::fetch("$q", $markers);
            } else {
                $row = ['seller_id' => $seller_id];

            }
            $form->inject($row);
        },
        'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $seller_id, $addressId) {

            if (true === $isUpdate) { // update
                QuickPdo::update("ek_seller_has_address", [
                    "order" => $fData['order'],
                ], [
                    ['seller_id', '=', $seller_id],
                    ['address_id', '=', $addressId],
                ]);
                $form->addNotification("L'adresse a bien été mise à jour", "success");
            } else {

                try {

                    QuickPdo::insert("ek_seller_has_address", [
                        "seller_id" => $fData['seller_id'],
                        "address_id" => $fData['address_id'],
                        "order" => $fData['order'],
                    ]);
                    $form->addNotification("L'adresse a bien été ajoutée", "success");
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
            'seller_id',
            'address_id',
        ],
    ];


} else {
    throw new EkomException("Some variables not found in the given context");
}