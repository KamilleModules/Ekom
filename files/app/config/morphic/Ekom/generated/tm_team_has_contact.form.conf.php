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

// inferred data (can be overridden by fkeys)
$shop_id = EkomNullosUser::getEkomValue("shop_id");
$lang_id = EkomNullosUser::getEkomValue("lang_id");
$currency_id = EkomNullosUser::getEkomValue("currency_id");

$choice_contact_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from tm_contact", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_team_id = QuickPdo::fetchAll("select id, concat(id, \". \", mailtype) as label from tm_team", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'team_id',
    'contact_id',
];

$contact_id = (array_key_exists("contact_id", $_GET)) ? $_GET['contact_id'] : null;
$team_id = (array_key_exists("team_id", $_GET)) ? $_GET['team_id'] : null;



$avatar = (array_key_exists("avatar", $context)) ? $context['avatar'] : null;

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
    'title' => "team-contact",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-tm_team_has_contact")
        ->addControl(SokoChoiceControl::create()
            ->setName("team_id")
            ->setLabel("Team id")
            ->setProperties([
                'readonly' => (null !== $team_id),
            ])
            ->setValue($team_id)
            ->setChoices($choice_team_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("contact_id")
            ->setLabel("Contact id")
            ->setProperties([
                'readonly' => (null !== $contact_id),
            ])
            ->setValue($contact_id)
            ->setChoices($choice_contact_id)),
    'feed' => MorphicHelper::getFeedFunction("tm_team_has_contact"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $team_id, $contact_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("tm_team_has_contact", [
				"team_id" => $fData["team_id"],
				"contact_id" => $fData["contact_id"],

            ], '', $ric);
            $form->addNotification("Le/la team-contact a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("tm_team_has_contact", [

            ], [
				["team_id", "=", $team_id],
				["contact_id", "=", $contact_id],
            
            ]);
            $form->addNotification("Le/la team-contact a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
