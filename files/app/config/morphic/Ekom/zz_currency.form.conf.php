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
    'title' => "Currency",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-currency")
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
            ->setName("iso_code")
            ->setLabel('Code iso')
            ->setProperties([
                'required' => false,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("symbol")
            ->setLabel('Symbole')
        )
        ->addValidationRule("iso_code", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_currency"),
    'process' => function ($fData, SokoFormInterface $form) {


        if (empty($fData['id'])) {
            QuickPdo::insert("ek_currency", [
                "iso_code" => $fData['iso_code'],
                "symbol" => $fData['symbol'],
            ]);
            $form->addNotification("La devise a bien été ajoutée", "success");
        } else {
            QuickPdo::update("ek_currency", [
                "iso_code" => $fData['iso_code'],
                "symbol" => $fData['symbol'],
            ], [
                ['id', '=', $fData['id']],
            ]);
            $form->addNotification("La devise a bien été mise à jour", "success");
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];