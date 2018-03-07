<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Object\FeatureLang;
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
$id = MorphicHelper::getFormContextValue("id", $context); // featureId
$langs = LangLayer::getLangItems();
$langId = (array_key_exists("lang_id", $_GET)) ? (int)$_GET['lang_id'] : 0;


$isReadOnly = (0 !== $langId);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product feature lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-feature_lang")
        ->addControl(SokoInputControl::create()
            ->setName("feature_id")
            ->setLabel('Product feature id')
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
            ->setName("name")
            ->setLabel('Name')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_feature_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($langId) {
        if (0 === $langId) {
            FeatureLang::getInst()->create($fData);
            $form->addNotification("La traduction pour cette caractéristique de produit a bien été ajoutée", "success");
        } else {
            FeatureLang::getInst()->update($fData, [
                "feature_id" => $fData['feature_id'],
                "lang_id" => $fData['lang_id'],
            ]);
            $form->addNotification("La traduction pour cette caractéristique de produit a bien été mise à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'feature_id',
        'lang_id',
    ],
];




