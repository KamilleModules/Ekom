<?php


use Module\Ekom\Api\Layer\CountryLayer;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;
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


$countryIdValue = (array_key_exists('country_id', $_GET)) ? $_GET['country_id'] : null;
$langIdValue = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;
$isUpdate = (null !== $langIdValue && null !== $countryIdValue);


//--------------------------------------------
// HYBRID CURRENCY CONTROL
//--------------------------------------------
$allCountries = CountryLayer::getCountryItems();
$allLangs = LangLayer::getLangItems();


$countryControl = SokoChoiceControl::create()
    ->setName("country_id")
    ->setLabel('Country id')
    ->setValue($countryIdValue)
    ->setChoices($allCountries);


$langControl = SokoChoiceControl::create()
    ->setName("lang_id")
    ->setLabel('Lang id')
    ->setValue($langIdValue)
    ->setChoices($allLangs);


if ($isUpdate) {
    $countryControl->setProperties([
        'readonly' => true,
    ]);
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
    'title' => "Country translation",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setAction("?" . http_build_query($_GET))
        ->setName("soko-form-country_translation")
        ->addControl($countryControl)
        ->addControl($langControl)
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel('Label')
            ->setProperties([
                'required' => false,
            ])
        )
        ->addValidationRule("label", SokoNotEmptyValidationRule::create())
    ,
    'feed' => function (SokoFormInterface $form, array $ric) use ($isUpdate, $countryIdValue, $langIdValue) {


        if (null !== $isUpdate) {

            $countryIdValue = (int)$countryIdValue;
            $langIdValue = (int)$langIdValue;


            $q = "select 
label
from ek_country_lang
where country_id=$countryIdValue and lang_id=$langIdValue

";
            $row = QuickPdo::fetch($q);
            if ($row) {
                $form->inject($row);
            }
        }
    },
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $countryIdValue, $langIdValue) {


        if (true === $isUpdate) { // update
            QuickPdo::update("ek_country_lang", [
                "label" => $fData['label'],
            ], [
                ['country_id', '=', $countryIdValue],
                ['lang_id', '=', $langIdValue],
            ]);
            $form->addNotification("La traduction de pays a bien été mise à jour", "success");


        } else {

            try {


                QuickPdo::insert("ek_country_lang", [
                    "label" => $fData['label'],
                    "country_id" => $fData['country_id'],
                    "lang_id" => $fData['lang_id'],
                ]);
                $form->addNotification("La traduction de pays a bien été ajoutée", "success");
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
        'country_id',
        'lang_id',
    ],
];