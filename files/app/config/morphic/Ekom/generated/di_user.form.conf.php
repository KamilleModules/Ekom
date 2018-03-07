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

$choice_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from di_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$group_id = (array_key_exists("group_id", $_GET)) ? $_GET['group_id'] : null;



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
    'title' => "user",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_user")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("group_id")
            ->setLabel("Group id")
            ->setProperties([
                'readonly' => (null !== $group_id),
            ])
            ->setValue($group_id)
            ->setChoices($choice_group_id))
        ->addControl(SokoInputControl::create()
            ->setName("email")
            ->setLabel("Email")
        )
        ->addControl(SokoInputControl::create()
            ->setName("token")
            ->setLabel("Token")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_started")
            ->setLabel("Date_started")
            ->addProperties([
                "required" => false,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("date_completed")
            ->setLabel("Date_completed")
        ),
    'feed' => MorphicHelper::getFeedFunction("di_user"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("di_user", [
				"group_id" => $fData["group_id"],
				"email" => $fData["email"],
				"token" => $fData["token"],
				"date_started" => $fData["date_started"],
				"date_completed" => $fData["date_completed"],

            ], '', $ric);
            $form->addNotification("Le/la user a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("di_user", [
				"group_id" => $fData["group_id"],
				"email" => $fData["email"],
				"token" => $fData["token"],
				"date_started" => $fData["date_started"],
				"date_completed" => $fData["date_completed"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la user a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_DiUserActionHistory_List") . "?s&user_id=$id",
                    "text" => "Voir les user action histories",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_DiUserHasElement_List") . "?s&user_id=$id",
                    "text" => "Voir les user-elements",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
