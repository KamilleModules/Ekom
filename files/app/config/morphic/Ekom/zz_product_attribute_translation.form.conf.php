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


$product_attributeIdValue = (array_key_exists('product_attribute_id', $_GET)) ? $_GET['product_attribute_id'] : null;
$idValue = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
$langIdValue = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;


$isUpdate = (null !== $langIdValue && null !== $product_attributeIdValue);



$product_attributeControlValue = (true===$isUpdate)?$product_attributeIdValue:$idValue;

//--------------------------------------------
// HYBRID CURRENCY CONTROL
//--------------------------------------------
$allProductAttributes = ProductAttributeLayer::getProductAttributeItems();
$allLangs = LangLayer::getLangItems();


$product_attributeControl = SokoChoiceControl::create()
    ->setName("product_attribute_id")
    ->setLabel('Product attribute id')
    ->setValue($product_attributeControlValue)
    ->setChoices($allProductAttributes);


$langControl = SokoChoiceControl::create()
    ->setName("lang_id")
    ->setLabel('Lang id')
    ->setValue($langIdValue)
    ->setChoices($allLangs);


if ($product_attributeIdValue) {
    $product_attributeControl->setProperties([
        'readonly' => true,
    ]);
    $langControl->setProperties([
        'readonly' => true,
    ]);
} elseif ($idValue) {
    $product_attributeControl->setProperties([
        'readonly' => true,
    ]);
}


$link = E::link("NullosAdmin_Ekom_ProductAttributeTranslation_List") . "?id=" . $product_attributeIdValue;


//--------------------------------------------
// CONF
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product attribute translation",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setAction("?" . http_build_query($_GET))
        ->setName("soko-form-product_attribute_translation")
        ->addControl($product_attributeControl)
        ->addControl($langControl)
        ->addControl(SokoInputControl::create()
            ->setName("name")
            ->setLabel('Label')
            ->setProperties([
                'required' => false,
            ])
        )
        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'forceFeed' => true,
    'feed' => function (SokoFormInterface $form, array $ric) use ($isUpdate, $idValue, $product_attributeIdValue, $langIdValue) {

        if (true === $isUpdate) {

            $product_attributeIdValue = (int)$product_attributeIdValue;
            $langIdValue = (int)$langIdValue;


            $q = "select 
name
from ek_product_attribute_lang
where product_attribute_id=$product_attributeIdValue and lang_id=$langIdValue

";
        } else {
            $q = "select 
name
from ek_product_attribute_lang
where product_attribute_id=$idValue

";
        }
        $row = QuickPdo::fetch($q);
        if (false !== $row) {
            $form->inject($row);
        }
    },
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $idValue, $product_attributeIdValue, $langIdValue) {


        if (true === $isUpdate) { // update
            QuickPdo::update("ek_product_attribute_lang", [
                "name" => $fData['name'],
            ], [
                ['product_attribute_id', '=', $product_attributeIdValue],
                ['lang_id', '=', $langIdValue],
            ]);
            $form->addNotification("La traduction de cet attribut de produit a bien été mise à jour", "success");


        } else {

            try {


                QuickPdo::insert("ek_product_attribute_lang", [
                    "name" => $fData['name'],
                    "product_attribute_id" => $fData['product_attribute_id'],
                    "lang_id" => $fData['lang_id'],
                ]);
                $form->addNotification("La traduction de cet attribut de produit a bien été ajoutée", "success");
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
        'product_attribute_id',
        'lang_id',
    ],
    'buttons' => [
        [
            "text" => "Ajouter une traduction pour cet attribut de produit",
            "link" => $link,
            "icon" => "fa fa-plus",
        ],
    ],
];