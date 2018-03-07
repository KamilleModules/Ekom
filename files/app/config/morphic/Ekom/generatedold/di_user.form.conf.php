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


$choice_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.di_group", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "User",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_user")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("group_id")
            ->setLabel('Group id')
            ->setChoices($choice_group_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoInputControl::create()
            ->setName("email")
            ->setLabel("Email")
        )
        ->addControl(SokoInputControl::create()
            ->setName("token")
            ->setLabel("Token")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_started")
            ->setLabel("Date_started")
            ->addProperties([
                "required" => false,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("date_completed")
            ->setLabel("Date_completed")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("di_user"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("di_user", [
				"group_id" => $fData["group_id"],
				"email" => $fData["email"],
				"token" => $fData["token"],
				"date_started" => $fData["date_started"],
				"date_completed" => $fData["date_completed"],

            ]);
            $form->addNotification("Le/la User a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("di_user", [
				"group_id" => $fData["group_id"],
				"email" => $fData["email"],
				"token" => $fData["token"],
				"date_started" => $fData["date_started"],
				"date_completed" => $fData["date_completed"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la User a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_DiUserHasElement_List") . "?user_id=$id",
                    "text" => "Voir les elements de ce/cette User",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


