<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Object\SellerHasAddress;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\Utils\E;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


//--------------------------------------------
// FORM WITH CONTEXT
//--------------------------------------------


$ric = [
    "seller_id",
    "address_id",
];


//--------------------------------------------
// foreach ric, except context keys
//--------------------------------------------
$seller_id = (array_key_exists('seller_id', $_GET)) ? $_GET['seller_id'] : null;
$address_id = (array_key_exists('address_id', $_GET)) ? $_GET['address_id'] : null;
// endforeach ric


$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$isUpdate = MorphicHelper::getIsUpdate($ric);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Seller has address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-seller_has_address")
        //--------------------------------------------
        // foreach ric keys
        //--------------------------------------------
        ->addControl(SokoInputControl::create()
            ->setName("seller_id")
            ->setLabel('Seller id')
            ->setValue($seller_id)
            ->setProperties([
                'readonly' => true,
            ])
        )
        // endforeach
        ->addControl(SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.address",
            ]))
            ->setName("address_id")
            ->setLabel('Address id')
            ->setValue($address_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel('Order')
        )
        //--------------------------------------------
        // foreach children keys
        //--------------------------------------------
        ->addValidationRule("address_id", SokoNotEmptyValidationRule::create()),
    // endforeach
    'feed' => MorphicHelper::getFeedFunction("ek_seller_has_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $seller_id, $address_id) {
        if (false === $isUpdate) {
            SellerHasAddress::getInst()->create($fData);
            $form->addNotification("L'adresse pour le vendeur \"$avatar\" a bien été ajoutée", "success");
        } else {
            SellerHasAddress::getInst()->update($fData, [
                //--------------------------------------------
                // foreach ric
                //--------------------------------------------
                "seller_id" => $fData['seller_id'],
                "address_id" => $fData['address_id'],
            ]);
            $form->addNotification("L'adresse pour le vendeur \"$avatar\" a bien été mise à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];




