<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Object\ProductCardLang;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


//--------------------------------------------
// FORM WITH CONTEXT
//--------------------------------------------
$id = MorphicHelper::getFormContextValue("id", $context); // productCardId
$langs = LangLayer::getLangItems();
$langId = (array_key_exists("lang_id", $_GET)) ? (int)$_GET['lang_id'] : 0;


$isReadOnly = (0 !== $langId);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product card lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-product_card_lang")
        ->addControl(SokoInputControl::create()
            ->setName("product_card_id")
            ->setLabel('Product card id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel('Lang id')
            ->setChoices($langs)
            ->setValue($langId)
            ->setProperties([
//                'disabled' => true,
                'readonly' => $isReadOnly,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel('Label')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("description")
            ->setLabel('Description')
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("slug")
            ->setLabel('Slug')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_title")
            ->setLabel('Meta title')
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_description")
            ->setLabel('Meta description')
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_keywords")
            ->setLabel('Meta keywords')
            ->setType("textarea")
        )
        ->addValidationRule("label", SokoNotEmptyValidationRule::create())
        ->addValidationRule("slug", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_product_card_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($langId) {
        if (0 === $langId) {
            ProductCardLang::getInst()->create($fData);
            $form->addNotification("La traduction pour cette carte produit a bien été ajoutée", "success");
        } else {
            ProductCardLang::getInst()->update($fData, [
                "product_card_id" => $fData['product_card_id'],
                "lang_id" => $fData['lang_id'],
            ]);
            $form->addNotification("La traduction pour cette carte produit a bien été mise à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'product_card_id',
        'lang_id',
    ],
];




