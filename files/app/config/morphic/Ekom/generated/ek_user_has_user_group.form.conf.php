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

$choice_user_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from ek_user", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_user_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_user_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'user_id',
    'user_group_id',
];

$user_id = (array_key_exists("user_id", $_GET)) ? $_GET['user_id'] : null;
$user_group_id = (array_key_exists("user_group_id", $_GET)) ? $_GET['user_group_id'] : null;



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
    'title' => "user-user group",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_user_has_user_group")
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => (null !== $user_id),
            ])
            ->setValue($user_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.user",
            ]))         )
        ->addControl(SokoChoiceControl::create()
            ->setName("user_group_id")
            ->setLabel("User group id")
            ->setProperties([
                'readonly' => (null !== $user_group_id),
            ])
            ->setValue($user_group_id)
            ->setChoices($choice_user_group_id)),
    'feed' => MorphicHelper::getFeedFunction("ek_user_has_user_group"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $user_id, $user_group_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_user_has_user_group", [
				"user_id" => $fData["user_id"],
				"user_group_id" => $fData["user_group_id"],

            ], '', $ric);
            $form->addNotification("Le/la user-user group a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_user_has_user_group", [

            ], [
				["user_id", "=", $user_id],
				["user_group_id", "=", $user_group_id],
            
            ]);
            $form->addNotification("Le/la user-user group a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
