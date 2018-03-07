<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\FeatureLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ProductAttributeLayer;
use Module\Ekom\Api\Object\ProductCardLang;
use Module\Ekom\Api\Object\ProductHasFeature;
use Module\Ekom\Api\Object\ProductHasProductAttribute;
use Module\Ekom\Api\Object\ProductLang;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;
use Module\NullosAdmin\SokoForm\Control\NullosSokoReactiveChoiceControl;
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
$shopId = EkomNullosUser::getEkomValue("shop_id");
$langId = EkomNullosUser::getEkomValue("lang_id");

$id = MorphicHelper::getFormContextValue("id", $context); // productId
$features = FeatureLayer::getItems($langId);
$featureValues = FeatureLayer::getValueItems($langId);
$featureId = (array_key_exists("feature_id", $_GET)) ? (int)$_GET['feature_id'] : 0;

$isReadOnly = (0 !== $featureId);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product has feature combination",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-product_has_feature")
        ->addControl(SokoInputControl::create()
            ->setName("product_id")
            ->setLabel('Product id')
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("feature_id")
            ->setLabel('Feature id')
            ->setChoices($features)
            ->setValue($featureId)
            ->setProperties([
                'readonly' => $isReadOnly,
            ])
        )
        ->addControl(NullosSokoReactiveChoiceControl::create()
            ->setName("feature_value_id")
            ->setLabel('Feature value id')
            ->setProperties([
                "listenTo" => "feature_id",
                "service" => "back.reactive.feature_value",
                "defaultLabel" => "Veuillez choisir une feature d'abord",
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("position")
            ->setLabel('Position')
        )
        ->addControl(SokoInputControl::create()
            ->setName("technical_description")
            ->setLabel('Technical description')
            ->setType("textarea")
        )
        ->addValidationRule("feature_value_id", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_product_has_feature"),
    'process' => function ($fData, SokoFormInterface $form) use ($featureId, $shopId) {
        $fData["shop_id"] = $shopId;
        if (0 === $featureId) {
            ProductHasFeature::getInst()->create($fData);
            $form->addNotification("La combinaison de caractéristique de produit a bien été ajoutée pour ce produit", "success");
        } else {
            ProductHasFeature::getInst()->update($fData, [
                "product_id" => $fData['product_id'],
                "feature_id" => $fData['feature_id'],
                "shop_id" => $shopId,
            ]);
            $form->addNotification("La combinaison de caractéristique de produit a bien été mise à jour pour ce produit", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'product_id',
        'feature_id',
//        'shop_id',
    ],
];




