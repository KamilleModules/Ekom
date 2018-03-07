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


$choice_location_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ekev_location", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "Event",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_event")
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
        ->addControl(EkomSokoDateControl::create()
            ->setName("start_date")
            ->setLabel('Start date')
        )
        ->addControl(EkomSokoDateControl::create()
            ->setName("end_date")
            ->setLabel('End date')
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("location_id")
            ->setLabel('Location id')
            ->setChoices($choice_location_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ekev_event"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_event", [
				"shop_id" => $fData["shop_id"],
				"name" => $fData["name"],
				"start_date" => $fData["start_date"],
				"end_date" => $fData["end_date"],
				"location_id" => $fData["location_id"],

            ]);
            $form->addNotification("Le/la Event a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_event", [
				"shop_id" => $fData["shop_id"],
				"name" => $fData["name"],
				"start_date" => $fData["start_date"],
				"end_date" => $fData["end_date"],
				"location_id" => $fData["location_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Event a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourse_List") . "?event_id=$id",
                    "text" => "Voir les courses de ce/cette Event",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEventHasCourseHasParticipant_List") . "?",
                    "text" => "Voir les participants de ce/cette Event",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


