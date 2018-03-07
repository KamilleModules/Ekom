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
    'title' => "Tax",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-tax")
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
            ->setName("amount")
            ->setLabel('Amount')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addValidationRule("amount", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunctionByQuery("select * from ek_tax where id=$value"),
    'process' => function ($fData, SokoFormInterface $form) {


        if (empty($fData['id'])) {
            QuickPdo::insert("ek_tax", [
                "amount" => $fData['amount'],
            ]);
            $form->addNotification("La taxe a bien été ajoutée", "success");
        } else {
            QuickPdo::update("ek_tax", [
                "amount" => $fData['amount'],
            ], [
                ['id', '=', $fData['id']],
            ]);
            $form->addNotification("La taxe a bien été mise à jour", "success");
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];