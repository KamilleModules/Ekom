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

$choice_trainer_id = QuickPdo::fetchAll("select id, concat(id, \". \", pseudo) as label from ektra_trainer", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_trainer_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ektra_trainer_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'trainer_group_id',
    'trainer_id',
];

$trainer_id = (array_key_exists("trainer_id", $_GET)) ? $_GET['trainer_id'] : null;
$trainer_group_id = (array_key_exists("trainer_group_id", $_GET)) ? $_GET['trainer_group_id'] : null;



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
    'title' => "trainer group-trainer",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_trainer_group_has_trainer")
        ->addControl(SokoChoiceControl::create()
            ->setName("trainer_group_id")
            ->setLabel("Trainer group id")
            ->setProperties([
                'readonly' => (null !== $trainer_group_id),
            ])
            ->setValue($trainer_group_id)
            ->setChoices($choice_trainer_group_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("trainer_id")
            ->setLabel("Trainer id")
            ->setProperties([
                'readonly' => (null !== $trainer_id),
            ])
            ->setValue($trainer_id)
            ->setChoices($choice_trainer_id)),
    'feed' => MorphicHelper::getFeedFunction("ektra_trainer_group_has_trainer"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $trainer_group_id, $trainer_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ektra_trainer_group_has_trainer", [
				"trainer_group_id" => $fData["trainer_group_id"],
				"trainer_id" => $fData["trainer_id"],

            ], '', $ric);
            $form->addNotification("Le/la trainer group-trainer a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ektra_trainer_group_has_trainer", [

            ], [
				["trainer_group_id", "=", $trainer_group_id],
				["trainer_id", "=", $trainer_id],
            
            ]);
            $form->addNotification("Le/la trainer group-trainer a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
