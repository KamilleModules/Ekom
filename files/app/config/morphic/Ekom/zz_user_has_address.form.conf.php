<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Object\UserHasAddress;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;


//--------------------------------------------
// FORM WITH CONTEXT
//--------------------------------------------
$id = MorphicHelper::getFormContextValue("id", $context); // userId
$langId = EkomNullosUser::getEkomValue("lang_id");
$userAddresses = AddressLayer::getEntries();
$addressId = (array_key_exists("address_id", $_GET)) ? (int)$_GET['address_id'] : 0;


$isReadOnly = (0!==$addressId);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "User has address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-user_has_address")
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel('User id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("address_id")
            ->setLabel('Address id')
            ->setChoices($userAddresses)
            ->setValue($addressId)
            ->setProperties([
//                'disabled' => true,
                'readonly' => $isReadOnly,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel('Order')
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default_shipping_address")
            ->setLabel('Is default shipping address')
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default_billing_address")
            ->setLabel('Is default billing address')
            ->setValue(1)
        )
//        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => function (SokoFormInterface $form, array $ric) {
        $markers = [];
        $values = array_intersect_key($_GET, array_flip($ric));
        $q = "select * from ek_user_has_address";
        QuickPdoStmtTool::addWhereEqualsSubStmt($values, $q, $markers);
        $row = QuickPdo::fetch("$q", $markers);
        $form->inject($row);
    },
    'process' => function ($fData, SokoFormInterface $form) use ($addressId) {
        if (0 === $addressId) {
            UserHasAddress::getInst()->create($fData);
            $form->addNotification("L'adresse a bien été ajoutée pour cet utilisateur", "success");
        } else {
            UserHasAddress::getInst()->update($fData, [
                "user_id" => $fData['user_id'],
                "address_id" => $fData['address_id'],
            ]);
            $form->addNotification("L'adresse a bien été mise à jour pour cet utilisateur", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'user_id',
        'address_id',
    ],
];




