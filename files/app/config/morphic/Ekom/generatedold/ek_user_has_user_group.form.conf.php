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


$choice_user_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_user_group", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'user_id',
    'user_group_id',
];
$user_id = MorphicHelper::getFormContextValue("user_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$user_group_id = (array_key_exists('user_group_id', $_GET)) ? $_GET['user_group_id'] : null;
        
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
    'title' => "User has user group",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_user_has_user_group")
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($user_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("user_group_id")
            ->setLabel('User group id')
            ->setChoices($choice_user_group_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($user_group_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_user_has_user_group"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $user_id, $user_group_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_user_has_user_group", [
				"user_id" => $fData["user_id"],
				"user_group_id" => $fData["user_group_id"],

            ]);
            $form->addNotification("Le/la User has user group pour le/la user \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_user_has_user_group", [

            ], [
				["user_id", "=", $user_id],
				["user_group_id", "=", $user_group_id],
            
            ]);
            $form->addNotification("Le/la User has user group pour le/la user \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


