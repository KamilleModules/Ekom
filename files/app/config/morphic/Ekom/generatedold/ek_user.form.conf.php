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
        ->setName("soko-form-ek_user")
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
            ->setName("pseudo")
            ->setLabel("Pseudo")
        )
        ->addControl(SokoInputControl::create()
            ->setName("first_name")
            ->setLabel("First_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("last_name")
            ->setLabel("Last_name")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_creation")
            ->setLabel("Date_creation")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("mobile")
            ->setLabel("Mobile")
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone")
            ->setLabel("Phone")
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone_prefix")
            ->setLabel("Phone_prefix")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("newsletter")
            ->setLabel("Newsletter")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("gender")
            ->setLabel("Gender")
            ->setValue(1)
        )
        ->addControl(EkomSokoDateControl::create()
            ->setName("birthday")
            ->setLabel('Birthday')
        )
        ->addControl(SokoInputControl::create()
            ->setName("active_hash")
            ->setLabel("Active_hash")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_user"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_user", [
				"shop_id" => $fData["shop_id"],
				"email" => $fData["email"],
				"pass" => $fData["pass"],
				"pseudo" => $fData["pseudo"],
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"date_creation" => $fData["date_creation"],
				"mobile" => $fData["mobile"],
				"phone" => $fData["phone"],
				"phone_prefix" => $fData["phone_prefix"],
				"newsletter" => $fData["newsletter"],
				"gender" => $fData["gender"],
				"birthday" => $fData["birthday"],
				"active_hash" => $fData["active_hash"],
				"active" => $fData["active"],

            ]);
            $form->addNotification("Le/la User a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_user", [
				"shop_id" => $fData["shop_id"],
				"email" => $fData["email"],
				"pass" => $fData["pass"],
				"pseudo" => $fData["pseudo"],
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"date_creation" => $fData["date_creation"],
				"mobile" => $fData["mobile"],
				"phone" => $fData["phone"],
				"phone_prefix" => $fData["phone_prefix"],
				"newsletter" => $fData["newsletter"],
				"gender" => $fData["gender"],
				"birthday" => $fData["birthday"],
				"active_hash" => $fData["active_hash"],
				"active" => $fData["active"],

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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserHasAddress_List") . "?user_id=$id",
                    "text" => "Voir les addresses de ce/cette User",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserHasProduct_List") . "?user_id=$id",
                    "text" => "Voir les products de ce/cette User",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserHasUserGroup_List") . "?user_id=$id",
                    "text" => "Voir les user groups de ce/cette User",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


