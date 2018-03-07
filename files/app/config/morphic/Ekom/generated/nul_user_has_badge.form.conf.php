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

$choice_badge_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from nul_badge", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_user_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from nul_user", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'user_id',
    'badge_id',
];

$badge_id = (array_key_exists("badge_id", $_GET)) ? $_GET['badge_id'] : null;
$user_id = (array_key_exists("user_id", $_GET)) ? $_GET['user_id'] : null;



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
    'title' => "user-badge",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-nul_user_has_badge")
        ->addControl(SokoChoiceControl::create()
            ->setName("user_id")
            ->setLabel("User id")
            ->setProperties([
                'readonly' => (null !== $user_id),
            ])
            ->setValue($user_id)
            ->setChoices($choice_user_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("badge_id")
            ->setLabel("Badge id")
            ->setProperties([
                'readonly' => (null !== $badge_id),
            ])
            ->setValue($badge_id)
            ->setChoices($choice_badge_id)),
    'feed' => MorphicHelper::getFeedFunction("nul_user_has_badge"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $user_id, $badge_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("nul_user_has_badge", [
				"user_id" => $fData["user_id"],
				"badge_id" => $fData["badge_id"],

            ], '', $ric);
            $form->addNotification("Le/la user-badge a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("nul_user_has_badge", [

            ], [
				["user_id", "=", $user_id],
				["badge_id", "=", $badge_id],
            
            ]);
            $form->addNotification("Le/la user-badge a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
