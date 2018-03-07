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


$choice_hotel_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ekev_hotel", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'location_id',
    'hotel_id',
];
$location_id = MorphicHelper::getFormContextValue("location_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$hotel_id = (array_key_exists('hotel_id', $_GET)) ? $_GET['hotel_id'] : null;
        
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
    'title' => "Location has hotel",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_location_has_hotel")
        ->addControl(SokoInputControl::create()
            ->setName("location_id")
            ->setLabel("Location id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($location_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("hotel_id")
            ->setLabel('Hotel id')
            ->setChoices($choice_hotel_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($hotel_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ekev_location_has_hotel"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $location_id, $hotel_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_location_has_hotel", [
				"location_id" => $fData["location_id"],
				"hotel_id" => $fData["hotel_id"],

            ]);
            $form->addNotification("Le/la Location has hotel pour le/la location \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_location_has_hotel", [

            ], [
				["location_id", "=", $location_id],
				["hotel_id", "=", $hotel_id],
            
            ]);
            $form->addNotification("Le/la Location has hotel pour le/la location \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


