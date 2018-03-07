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


$choice_element_id = QuickPdo::fetchAll("select id, concat(id, \". \", type) as label from kamille.di_element", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'user_id',
    'element_id',
];
$user_id = MorphicHelper::getFormContextValue("user_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$element_id = (array_key_exists('element_id', $_GET)) ? $_GET['element_id'] : null;
        
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
    'title' => "User has element",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_user_has_element")
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($user_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("element_id")
            ->setLabel('Element id')
            ->setChoices($choice_element_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($element_id)
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_completed")
            ->setLabel("Date_completed")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("value")
            ->setLabel("Value")
            ->setType("textarea")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("di_user_has_element"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $user_id, $element_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("di_user_has_element", [
				"user_id" => $fData["user_id"],
				"element_id" => $fData["element_id"],
				"date_completed" => $fData["date_completed"],
				"value" => $fData["value"],

            ]);
            $form->addNotification("Le/la User has element pour le/la user \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("di_user_has_element", [
				"date_completed" => $fData["date_completed"],
				"value" => $fData["value"],

            ], [
				["user_id", "=", $user_id],
				["element_id", "=", $element_id],
            
            ]);
            $form->addNotification("Le/la User has element pour le/la user \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


