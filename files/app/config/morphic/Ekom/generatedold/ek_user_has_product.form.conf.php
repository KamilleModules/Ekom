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
$user_id = MorphicHelper::getFormContextValue("user_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
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
    'title' => "User has product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_user_has_product")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($user_id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))    
            ->setName("product_id")
            ->setLabel("Product id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("product_details")
            ->setLabel("Product_details")
            ->setType("textarea")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date")
            ->setLabel("Date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("deleted_date")
            ->setLabel("Deleted_date")
            ->addProperties([
                "required" => false,                       
            ])
                        
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_user_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_user_has_product", [
				"user_id" => $fData["user_id"],
				"product_id" => $fData["product_id"],
				"product_details" => $fData["product_details"],
				"date" => $fData["date"],
				"deleted_date" => $fData["deleted_date"],

            ]);
            $form->addNotification("Le/la User has product pour le/la user \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_user_has_product", [
				"user_id" => $fData["user_id"],
				"product_id" => $fData["product_id"],
				"product_details" => $fData["product_details"],
				"date" => $fData["date"],
				"deleted_date" => $fData["deleted_date"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la User has product pour le/la user \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


