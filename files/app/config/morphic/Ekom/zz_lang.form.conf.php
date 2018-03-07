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
    'title' => "Lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-lang")
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
            ->setName("label")
            ->setLabel('Label')
        )
        ->addControl(SokoInputControl::create()
            ->setName("iso_code")
            ->setLabel('Code iso')
            ->setProperties([
                'required' => false,
            ])
        )
        ->addValidationRule("iso_code", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_lang"),
    'process' => function ($fData, SokoFormInterface $form) {


        if (empty($fData['id'])) {
            QuickPdo::insert("ek_lang", [
                "label" => $fData['label'],
                "iso_code" => $fData['iso_code'],
            ]);
            $form->addNotification("La langue a bien été ajoutée", "success");
        } else {
            QuickPdo::update("ek_lang", [
                "label" => $fData['label'],
                "iso_code" => $fData['iso_code'],
            ], [
                ['id', '=', $fData['id']],
            ]);
            $form->addNotification("La langue a bien été mise à jour", "success");
        }

        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
];