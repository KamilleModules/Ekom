<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\CountryLayer;
use Module\Ekom\Api\Object\Address;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$value = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;


$allItems = CountryLayer::getCountryItems();
$countryControl = SokoChoiceControl::create()
    ->setName("country_id")
    ->setLabel('Country id')
    ->setChoices($allItems);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-address")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel('Id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($value)
        )
        ->addControl(SokoInputControl::create()
            ->setName("first_name")
            ->setLabel('First name')
        )
        ->addControl(SokoInputControl::create()
            ->setName("last_name")
            ->setLabel('Last name')
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone")
            ->setLabel('Phone')
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone_prefix")
            ->setLabel('Phone prefix')
        )
        ->addControl(SokoInputControl::create()
            ->setName("address")
            ->setLabel('Address')
        )
        ->addControl(SokoInputControl::create()
            ->setName("city")
            ->setLabel('City')
        )
        ->addControl(SokoInputControl::create()
            ->setName("postcode")
            ->setLabel('Post code')
        )
        ->addControl(SokoInputControl::create()
            ->setName("supplement")
            ->setLabel('Supplement')
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel('Active')
            ->setValue(1)
        )
        ->addControl($countryControl)
        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($value) {

        if (empty($fData['id'])) {
            Address::getInst()->create($fData);
            $form->addNotification("L'adresse a bien été ajoutée", "success");
        } else {
            Address::getInst()->update($fData, [
                "id" => $fData['id'],
            ]);
            $form->addNotification("L'adresse a bien été mise à jour", "success");
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];