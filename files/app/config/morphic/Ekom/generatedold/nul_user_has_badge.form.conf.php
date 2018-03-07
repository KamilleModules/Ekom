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


$choice_badge_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.nul_badge", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'user_id',
    'badge_id',
];
$user_id = MorphicHelper::getFormContextValue("user_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$badge_id = (array_key_exists('badge_id', $_GET)) ? $_GET['badge_id'] : null;
        
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
    'title' => "User has badge",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-nul_user_has_badge")
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($user_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("badge_id")
            ->setLabel('Badge id')
            ->setChoices($choice_badge_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($badge_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("nul_user_has_badge"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $user_id, $badge_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("nul_user_has_badge", [
				"user_id" => $fData["user_id"],
				"badge_id" => $fData["badge_id"],

            ]);
            $form->addNotification("Le/la User has badge pour le/la user \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("nul_user_has_badge", [

            ], [
				["user_id", "=", $user_id],
				["badge_id", "=", $badge_id],
            
            ]);
            $form->addNotification("Le/la User has badge pour le/la user \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


