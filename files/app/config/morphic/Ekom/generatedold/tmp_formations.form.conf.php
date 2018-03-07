<?php 

use QuickPdo\QuickPdo;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use SokoForm\Form\SokoFormInterface;
use SokoForm\Form\SokoForm;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoBooleanChoiceControl;
use Module\Ekom\Utils\E;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\SokoForm\Control\EkomSokoDateControl;




//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'reference',
    'date',
    'location',
];
$reference = (array_key_exists('reference', $_GET)) ? $_GET['reference'] : null;
$date = (array_key_exists('date', $_GET)) ? $_GET['date'] : null;
$location = (array_key_exists('location', $_GET)) ? $_GET['location'] : null;
        
//--------------------------------------------
// UPDATE|INSERT MODE
//--------------------------------------------
$isUpdate = MorphicHelper::getIsUpdate($ric);
        
//--------------------------------------------
// FORM
//--------------------------------------------
$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Tmp formations",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-tmp_formations")
        ->addControl(SokoInputControl::create()
            ->setName("reference")
            ->setLabel("Reference")
        )
        ->addControl(EkomSokoDateControl::create()
            ->setName("date")
            ->setLabel('Date')
        )
        ->addControl(SokoInputControl::create()
            ->setName("location")
            ->setLabel("Location")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("tmp_formations"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $reference, $date, $location) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("tmp_formations", [
				"reference" => $fData["reference"],
				"date" => $fData["date"],
				"location" => $fData["location"],

            ]);
            $form->addNotification("Le/la Tmp formations a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("tmp_formations", [
				"reference" => $fData["reference"],
				"date" => $fData["date"],
				"location" => $fData["location"],

            ], [
				["reference", "=", $reference],
				["date", "=", $date],
				["location", "=", $location],
            
            ]);
            $form->addNotification("Le/la Tmp formations a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


