<?php


use Module\Ekom\Api\Layer\CountryLayer;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ProductAttributeLayer;
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


$product_attribute_valueIdValue = (array_key_exists('product_attribute_value_id', $_GET)) ? $_GET['product_attribute_value_id'] : null;
$idValue = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
$langIdValue = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;


$isUpdate = (null !== $langIdValue && null !== $product_attribute_valueIdValue);


$product_attribute_valueControlValue = (true === $isUpdate) ? $product_attribute_valueIdValue : $idValue;

//--------------------------------------------
// HYBRID CURRENCY CONTROL
//--------------------------------------------
$allProductAttributeValues = ProductAttributeLayer::getProductAttributeValueItems();
$allLangs = LangLayer::getLangItems();


$product_attribute_valueControl = SokoChoiceControl::create()
    ->setName("product_attribute_value_id")
    ->setLabel('Product attribute value id')
    ->setValue($product_attribute_valueControlValue)
    ->setChoices($allProductAttributeValues);


$langControl = SokoChoiceControl::create()
    ->setName("lang_id")
    ->setLabel('Lang id')
    ->setValue($langIdValue)
    ->setChoices($allLangs);


if ($product_attribute_valueIdValue) {
    $product_attribute_valueControl->setProperties([
        'readonly' => true,
    ]);
    $langControl->setProperties([
        'readonly' => true,
    ]);
} elseif ($idValue) {
    $product_attribute_valueControl->setProperties([
        'readonly' => true,
    ]);
}


$link = E::link("NullosAdmin_Ekom_ProductAttributeValueTranslation_List") . "?id=" . $product_attribute_valueIdValue;


//--------------------------------------------
// CONF
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product attribute value translation",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setAction("?" . http_build_query($_GET))
        ->setName("soko-form-product_attribute_value_translation")
        ->addControl($product_attribute_valueControl)
        ->addControl($langControl)
        ->addControl(SokoInputControl::create()
            ->setName("value")
            ->setLabel('Label')
            ->setProperties([
                'required' => false,
            ])
        )
        ->addValidationRule("value", SokoNotEmptyValidationRule::create())
    ,
    'forceFeed' => true,
    'feed' => function (SokoFormInterface $form, array $ric) use ($isUpdate, $idValue, $product_attribute_valueIdValue, $langIdValue) {

        if (true === $isUpdate) {

            $product_attribute_valueIdValue = (int)$product_attribute_valueIdValue;
            $langIdValue = (int)$langIdValue;


            $q = "select 
value
from ek_product_attribute_value_lang
where product_attribute_value_id=$product_attribute_valueIdValue and lang_id=$langIdValue

";
        } else {
            $q = "select 
value
from ek_product_attribute_value_lang
where product_attribute_value_id=$idValue

";
        }
        $row = QuickPdo::fetch($q);
        if (false !== $row) {
            $form->inject($row);
        }
    },
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $idValue, $product_attribute_valueIdValue, $langIdValue) {


        if (true === $isUpdate) { // update
            QuickPdo::update("ek_product_attribute_value_lang", [
                "value" => $fData['value'],
            ], [
                ['product_attribute_value_id', '=', $product_attribute_valueIdValue],
                ['lang_id', '=', $langIdValue],
            ]);
            $form->addNotification("La traduction de cette valeur d'attribut de produit a bien été mise à jour", "success");


        } else {

            try {


                QuickPdo::insert("ek_product_attribute_value_lang", [
                    "value" => $fData['value'],
                    "product_attribute_value_id" => $fData['product_attribute_value_id'],
                    "lang_id" => $fData['lang_id'],
                ]);
                $form->addNotification("La traduction de cette valeur d'attribut de produit a bien été ajoutée", "success");
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
        'product_attribute_value_id',
        'lang_id',
    ],
    'buttons' => [
        [
            "text" => "Ajouter une traduction pour cette valeur d'attribut de produit",
            "link" => $link,
            "icon" => "fa fa-plus",
        ],
    ],
];