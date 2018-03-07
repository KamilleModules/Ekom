<?php


use Module\Ekom\Api\Layer\CountryLayer;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\TaxLayer;
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


$taxIdValue = (array_key_exists('tax_id', $_GET)) ? $_GET['tax_id'] : null;
$idValue = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
$langIdValue = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;


$isUpdate = (null !== $langIdValue && null !== $taxIdValue);



$taxControlValue = (true===$isUpdate)?$taxIdValue:$idValue;

//--------------------------------------------
// HYBRID CURRENCY CONTROL
//--------------------------------------------
$allTaxes = TaxLayer::getTaxItems();
$allLangs = LangLayer::getLangItems();


$taxControl = SokoChoiceControl::create()
    ->setName("tax_id")
    ->setLabel('Tax id')
    ->setValue($taxControlValue)
    ->setChoices($allTaxes);


$langControl = SokoChoiceControl::create()
    ->setName("lang_id")
    ->setLabel('Lang id')
    ->setValue($langIdValue)
    ->setChoices($allLangs);


if ($taxIdValue) {
    $taxControl->setProperties([
        'readonly' => true,
    ]);
    $langControl->setProperties([
        'readonly' => true,
    ]);
} elseif ($idValue) {
    $taxControl->setProperties([
        'readonly' => true,
    ]);
}


$link = E::link("NullosAdmin_Ekom_TaxTranslation_List") . "?id=" . $taxIdValue;


//--------------------------------------------
// CONF
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Tax translation",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setAction("?" . http_build_query($_GET))
        ->setName("soko-form-tax_translation")
        ->addControl($taxControl)
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
    'forceFeed' => true,
    'feed' => function (SokoFormInterface $form, array $ric) use ($isUpdate, $idValue, $taxIdValue, $langIdValue) {

        if (true === $isUpdate) {

            $taxIdValue = (int)$taxIdValue;
            $langIdValue = (int)$langIdValue;


            $q = "select 
label
from ek_tax_lang
where tax_id=$taxIdValue and lang_id=$langIdValue

";
        } else {
            $q = "select 
label
from ek_tax_lang
where tax_id=$idValue

";
        }
        $row = QuickPdo::fetch($q);
        if (false !== $row) {
            $form->inject($row);
        }
    },
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $idValue, $taxIdValue, $langIdValue) {


        if (true === $isUpdate) { // update
            QuickPdo::update("ek_tax_lang", [
                "label" => $fData['label'],
            ], [
                ['tax_id', '=', $taxIdValue],
                ['lang_id', '=', $langIdValue],
            ]);
            $form->addNotification("La traduction de taxe a bien été mise à jour", "success");


        } else {

            try {


                QuickPdo::insert("ek_tax_lang", [
                    "label" => $fData['label'],
                    "tax_id" => $fData['tax_id'],
                    "lang_id" => $fData['lang_id'],
                ]);
                $form->addNotification("La traduction de taxe a bien été ajoutée", "success");
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
        'tax_id',
        'lang_id',
    ],
    'buttons' => [
        [
            "text" => "Ajouter une traduction pour cette taxe",
            "link" => $link,
            "icon" => "fa fa-plus",
        ],
    ],
];