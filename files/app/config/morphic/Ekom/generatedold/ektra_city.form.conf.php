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


$choice_country_id = QuickPdo::fetchAll("select id, concat(id, \". \", iso_code) as label from kamille.ek_country", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "City",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_city")
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
            ->setName("label")
            ->setLabel("Label")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("country_id")
            ->setLabel('Country id')
            ->setChoices($choice_country_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ektra_city"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ektra_city", [
				"name" => $fData["name"],
				"label" => $fData["label"],
				"country_id" => $fData["country_id"],

            ]);
            $form->addNotification("Le/la City a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ektra_city", [
				"name" => $fData["name"],
				"label" => $fData["label"],
				"country_id" => $fData["country_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la City a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


