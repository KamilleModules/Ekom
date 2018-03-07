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

$choice_city_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ektra_city", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_trainer_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ektra_trainer_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_training_id = QuickPdo::fetchAll("select id, concat(id, \". \", prerequisites) as label from ektra_training", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$city_id = (array_key_exists("city_id", $_GET)) ? $_GET['city_id'] : null;
$trainer_group_id = (array_key_exists("trainer_group_id", $_GET)) ? $_GET['trainer_group_id'] : null;
$training_id = (array_key_exists("training_id", $_GET)) ? $_GET['training_id'] : null;



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
    'title' => "training session",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_training_session")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("training_id")
            ->setLabel("Training id")
            ->setProperties([
                'readonly' => (null !== $training_id),
            ])
            ->setValue($training_id)
            ->setChoices($choice_training_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("trainer_group_id")
            ->setLabel("Trainer group id")
            ->setProperties([
                'readonly' => (null !== $trainer_group_id),
            ])
            ->setValue($trainer_group_id)
            ->setChoices($choice_trainer_group_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("city_id")
            ->setLabel("City id")
            ->setProperties([
                'readonly' => (null !== $city_id),
            ])
            ->setValue($city_id)
            ->setChoices($choice_city_id))
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default")
            ->setLabel("Is_default")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("capacity")
            ->setLabel("Capacity")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        ),
    'feed' => MorphicHelper::getFeedFunction("ektra_training_session"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ektra_training_session", [
				"training_id" => $fData["training_id"],
				"trainer_group_id" => $fData["trainer_group_id"],
				"city_id" => $fData["city_id"],
				"start_date" => $fData["start_date"],
				"end_date" => $fData["end_date"],
				"is_default" => $fData["is_default"],
				"capacity" => $fData["capacity"],
				"active" => $fData["active"],

            ], '', $ric);
            $form->addNotification("Le/la training session a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ektra_training_session", [
				"training_id" => $fData["training_id"],
				"trainer_group_id" => $fData["trainer_group_id"],
				"city_id" => $fData["city_id"],
				"start_date" => $fData["start_date"],
				"end_date" => $fData["end_date"],
				"is_default" => $fData["is_default"],
				"capacity" => $fData["capacity"],
				"active" => $fData["active"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la training session a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,            
    //--------------------------------------------
    // CHILDREN
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List") . "?s&training_session_id=$id",
                    "text" => "Voir les training session-participants",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
