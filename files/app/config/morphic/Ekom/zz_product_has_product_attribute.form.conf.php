<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ProductAttributeLayer;
use Module\Ekom\Api\Object\ProductCardLang;
use Module\Ekom\Api\Object\ProductHasProductAttribute;
use Module\Ekom\Api\Object\ProductLang;
use Module\Ekom\Utils\E;
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
$attributes = ProductAttributeLayer::getProductAttributeItems();
$attributeValues = ProductAttributeLayer::getProductAttributeValueItems();
$productAttributeId = (array_key_exists("product_attribute_id", $_GET)) ? (int)$_GET['product_attribute_id'] : 0;
$productAttributeValueId = (array_key_exists("product_attribute_value_id", $_GET)) ? (int)$_GET['product_attribute_value_id'] : 0;


$isReadOnly = (0 !== $productAttributeId);








$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product attribute combination",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-product_has_product_attribute")
        ->addControl(SokoInputControl::create()
            ->setName("product_id")
            ->setLabel('Product id')
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("product_attribute_id")
            ->setLabel('Product attribute id')
            ->setChoices($attributes)
            ->setValue($productAttributeId)
            ->setProperties([
                'readonly' => $isReadOnly,
            ])
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("product_attribute_value_id")
            ->setLabel('Product attribute value id')
            ->setChoices($attributeValues)
            ->setValue($productAttributeValueId)
//            ->setProperties([
//                'readonly' => $isReadOnly,
//            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel('Order')
        )
//        ->addValidationRule("label", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_product_has_product_attribute"),
    'process' => function ($fData, SokoFormInterface $form) use ($productAttributeId) {
        if (0 === $productAttributeId) {
            ProductHasProductAttribute::getInst()->create($fData);
            $form->addNotification("La combinaison d'attributs a bien été ajoutée pour ce produit", "success");
        } else {
            ProductHasProductAttribute::getInst()->update($fData, [
                "product_id" => $fData['product_id'],
                "product_attribute_id" => $fData['product_attribute_id'],
            ]);
            $form->addNotification("La combinaison d'attributs a bien été mise à jour pour ce produit", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'product_id',
        'product_attribute_id',
        'product_attribute_value_id',
    ],
];




