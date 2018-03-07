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
    'title' => "Product attribute",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-product_attribute")
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
            ->setName("name")
            ->setLabel('Name')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunctionByQuery("select * from ek_product_attribute where id=$value"),
    'process' => function ($fData, SokoFormInterface $form) {


        if (empty($fData['id'])) {
            QuickPdo::insert("ek_product_attribute", [
                "name" => $fData['name'],
            ]);
            $form->addNotification("L'attribut de produit a bien été ajouté", "success");
        } else {
            QuickPdo::update("ek_product_attribute", [
                "name" => $fData['name'],
            ], [
                ['id', '=', $fData['id']],
            ]);
            $form->addNotification("L'attribut de produit a bien été mis à jour", "success");
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];