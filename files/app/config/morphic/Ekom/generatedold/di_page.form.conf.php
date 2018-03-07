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
    'id',
];
$id = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
        
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
    'title' => "Page",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_page")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("name")
            ->setLabel("Name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("bg_document")
            ->setLabel("Bg_document")
        )
        ->addControl(SokoInputControl::create()
            ->setName("thumb")
            ->setLabel("Thumb")
        )
        ->addControl(SokoInputControl::create()
            ->setName("width")
            ->setLabel("Width")
        )
        ->addControl(SokoInputControl::create()
            ->setName("height")
            ->setLabel("Height")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("di_page"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("di_page", [
				"name" => $fData["name"],
				"bg_document" => $fData["bg_document"],
				"thumb" => $fData["thumb"],
				"width" => $fData["width"],
				"height" => $fData["height"],

            ]);
            $form->addNotification("Le/la Page a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("di_page", [
				"name" => $fData["name"],
				"bg_document" => $fData["bg_document"],
				"thumb" => $fData["thumb"],
				"width" => $fData["width"],
				"height" => $fData["height"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Page a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


