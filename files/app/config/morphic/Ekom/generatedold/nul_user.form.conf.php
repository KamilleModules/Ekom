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
    'title' => "User",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-nul_user")
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
            ->setName("pass")
            ->setLabel("Pass")
        )
        ->addControl(SokoInputControl::create()
            ->setName("avatar")
            ->setLabel("Avatar")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pseudo")
            ->setLabel("Pseudo")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_created")
            ->setLabel("Date_created")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_last_connexion")
            ->setLabel("Date_last_connexion")
            ->addProperties([
                "required" => false,                       
            ])
                        
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("nul_user"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("nul_user", [
				"email" => $fData["email"],
				"pass" => $fData["pass"],
				"avatar" => $fData["avatar"],
				"pseudo" => $fData["pseudo"],
				"active" => $fData["active"],
				"date_created" => $fData["date_created"],
				"date_last_connexion" => $fData["date_last_connexion"],

            ]);
            $form->addNotification("Le/la User a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("nul_user", [
				"email" => $fData["email"],
				"pass" => $fData["pass"],
				"avatar" => $fData["avatar"],
				"pseudo" => $fData["pseudo"],
				"active" => $fData["active"],
				"date_created" => $fData["date_created"],
				"date_last_connexion" => $fData["date_last_connexion"],

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
                    "link" => E::link("NullosAdmin_Ekom_Generated_NulUserHasBadge_List") . "?user_id=$id",
                    "text" => "Voir les badges de ce/cette User",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


