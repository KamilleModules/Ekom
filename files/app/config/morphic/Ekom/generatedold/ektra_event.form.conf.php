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


$choice_location_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ektra_location", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_date_range_id = QuickPdo::fetchAll("select id, concat(id, \". \", shop_id) as label from kamille.ektra_date_range", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_trainer_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ektra_trainer_group", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
        ->setName("soko-form-ektra_event")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))    
            ->setName("product_id")
            ->setLabel("Product id")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("location_id")
            ->setLabel('Location id')
            ->setChoices($choice_location_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("date_range_id")
            ->setLabel('Date range id')
            ->setChoices($choice_date_range_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("trainer_group_id")
            ->setLabel('Trainer group id')
            ->setChoices($choice_trainer_group_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop_id")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ektra_event"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ektra_event", [
				"product_id" => $fData["product_id"],
				"location_id" => $fData["location_id"],
				"date_range_id" => $fData["date_range_id"],
				"trainer_group_id" => $fData["trainer_group_id"],
				"shop_id" => $fData["shop_id"],

            ]);
            $form->addNotification("Le/la Event a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ektra_event", [
				"product_id" => $fData["product_id"],
				"location_id" => $fData["location_id"],
				"date_range_id" => $fData["date_range_id"],
				"trainer_group_id" => $fData["trainer_group_id"],
				"shop_id" => $fData["shop_id"],

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
];


