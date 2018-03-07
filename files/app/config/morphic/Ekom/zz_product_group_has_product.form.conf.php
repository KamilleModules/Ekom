<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Object\ProductCardLang;
use Module\Ekom\Api\Object\ProductGroupHasProduct;
use Module\Ekom\Api\Object\ProductLang;
use Module\Ekom\Back\Helper\BackFormHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;






//--------------------------------------------
// FORM WITH CONTEXT
//--------------------------------------------
$id = MorphicHelper::getFormContextValue("id", $context); // productGroupId
$productId = (array_key_exists("product_id", $_GET)) ? (int)$_GET['product_id'] : 0;
$isReadOnly = (0 !== $productId);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product group has product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-product_group_has_product")
        ->addControl(SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))
            ->setName("product_id")
            ->setLabel('Product id')
            ->setValue($productId)
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel('Order')
        )
        ->addValidationRule("product_id", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_product_group_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($id, $productId) {
        $fData['product_group_id'] = $id;
        if (0 === $productId) {
            ProductGroupHasProduct::getInst()->create($fData);
            $form->addNotification("Le produit a bien été ajouté dans ce groupe", "success");
        } else {
            ProductGroupHasProduct::getInst()->update($fData, [
                "product_group_id" => $id,
                "product_id" => $fData['product_id'],
            ]);
            $form->addNotification("La produit a bien été mis à jour pour ce groupe", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'product_group_id',
        'product_id',
    ],
];




