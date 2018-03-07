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


$choice_contact_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.tm_contact", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'team_id',
    'contact_id',
];
$team_id = MorphicHelper::getFormContextValue("team_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$contact_id = (array_key_exists('contact_id', $_GET)) ? $_GET['contact_id'] : null;
        
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
    'title' => "Team has contact",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-tm_team_has_contact")
        ->addControl(SokoInputControl::create()
            ->setName("team_id")
            ->setLabel("Team id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($team_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("contact_id")
            ->setLabel('Contact id')
            ->setChoices($choice_contact_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($contact_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("tm_team_has_contact"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $team_id, $contact_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("tm_team_has_contact", [
				"team_id" => $fData["team_id"],
				"contact_id" => $fData["contact_id"],

            ]);
            $form->addNotification("Le/la Team has contact pour le/la team \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("tm_team_has_contact", [

            ], [
				["team_id", "=", $team_id],
				["contact_id", "=", $contact_id],
            
            ]);
            $form->addNotification("Le/la Team has contact pour le/la team \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


