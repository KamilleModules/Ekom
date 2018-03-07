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
    'title' => "Participant",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_participant")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("email")
            ->setLabel("Email")
        )
        ->addControl(SokoInputControl::create()
            ->setName("first_name")
            ->setLabel("First_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("last_name")
            ->setLabel("Last_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("address")
            ->setLabel("Address")
        )
        ->addControl(SokoInputControl::create()
            ->setName("city")
            ->setLabel("City")
        )
        ->addControl(SokoInputControl::create()
            ->setName("postcode")
            ->setLabel("Postcode")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("country_id")
            ->setLabel('Country id')
            ->setChoices($choice_country_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone")
            ->setLabel("Phone")
        )
        ->addControl(SokoInputControl::create()
            ->setName("birthday")
            ->setLabel("Birthday")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ekev_participant"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_participant", [
				"email" => $fData["email"],
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"address" => $fData["address"],
				"city" => $fData["city"],
				"postcode" => $fData["postcode"],
				"country_id" => $fData["country_id"],
				"phone" => $fData["phone"],
				"birthday" => $fData["birthday"],

            ]);
            $form->addNotification("Le/la Participant a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_participant", [
				"email" => $fData["email"],
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"address" => $fData["address"],
				"city" => $fData["city"],
				"postcode" => $fData["postcode"],
				"country_id" => $fData["country_id"],
				"phone" => $fData["phone"],
				"birthday" => $fData["birthday"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Participant a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


