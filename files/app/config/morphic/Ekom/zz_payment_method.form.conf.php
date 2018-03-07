<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$value = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;

$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Payment method",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-payment_method")
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
                'required' => false,
            ])
        )
        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_payment_method"),
    'process' => function ($fData, SokoFormInterface $form) {


        if (empty($fData['id'])) {
            QuickPdo::insert("ek_payment_method", [
                "name" => $fData['name'],
            ]);
            $form->addNotification("La méthode de paiement a bien été ajoutée", "success");
        } else {
            QuickPdo::update("ek_payment_method", [
                "name" => $fData['name'],
            ], [
                ['id', '=', $fData['id']],
            ]);
            $form->addNotification("La méthode de paiement a bien été mise à jour", "success");
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];