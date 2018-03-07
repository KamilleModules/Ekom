<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$value = (array_key_exists('id', $_GET)) ? (int)$_GET['id'] : null;

$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product attribute value",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-product_attribute_value")
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
            ->setName("value")
            ->setLabel('Value')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addValidationRule("value", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunctionByQuery("select * from ek_product_attribute_value where id=$value"),
    'process' => function ($fData, SokoFormInterface $form) {


        if (empty($fData['id'])) {
            QuickPdo::insert("ek_product_attribute_value", [
                "value" => $fData['value'],
            ]);
            $form->addNotification("La valeur d'attribut de produit a bien été ajoutée", "success");
        } else {
            QuickPdo::update("ek_product_attribute_value", [
                "value" => $fData['value'],
            ], [
                ['id', '=', $fData['id']],
            ]);
            $form->addNotification("La valeur d'attribut de produit a bien été mise à jour", "success");
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];