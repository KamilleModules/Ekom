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
    'title' => "Location",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_location")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel("Label")
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
        ->addControl(SokoInputControl::create()
            ->setName("phone")
            ->setLabel("Phone")
        )
        ->addControl(SokoInputControl::create()
            ->setName("extra")
            ->setLabel("Extra")
            ->setType("textarea")
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
    'feed' => MorphicHelper::getFeedFunction("ekev_location"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_location", [
				"label" => $fData["label"],
				"address" => $fData["address"],
				"city" => $fData["city"],
				"postcode" => $fData["postcode"],
				"phone" => $fData["phone"],
				"extra" => $fData["extra"],
				"country_id" => $fData["country_id"],
				"shop_id" => $fData["shop_id"],

            ]);
            $form->addNotification("Le/la Location a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_location", [
				"label" => $fData["label"],
				"address" => $fData["address"],
				"city" => $fData["city"],
				"postcode" => $fData["postcode"],
				"phone" => $fData["phone"],
				"extra" => $fData["extra"],
				"country_id" => $fData["country_id"],
				"shop_id" => $fData["shop_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Location a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
    //--------------------------------------------
    // IF HAS CONTEXT
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevLocationHasHotel_List") . "?location_id=$id",
                    "text" => "Voir les hotels de ce/cette Location",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


