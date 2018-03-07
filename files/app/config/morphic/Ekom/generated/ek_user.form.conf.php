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

$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$shop_id = (array_key_exists("shop_id", $_GET)) ? $_GET['shop_id'] : $shop_id; // inferred



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
        ->setName("soko-form-ek_user")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoInputControl::create()
            ->setName("email")
            ->setLabel("Email")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pass")
            ->setLabel("Pass")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pseudo")
            ->setLabel("Pseudo")
        )
        ->addControl(SokoInputControl::create()
            ->setName("first_name")
            ->setLabel("First_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("last_name")
            ->setLabel("Last_name")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_creation")
            ->setLabel("Date_creation")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("mobile")
            ->setLabel("Mobile")
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone")
            ->setLabel("Phone")
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone_prefix")
            ->setLabel("Phone_prefix")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("newsletter")
            ->setLabel("Newsletter")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("gender")
            ->setLabel("Gender")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("active_hash")
            ->setLabel("Active_hash")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_user"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_user", [
				"shop_id" => $fData["shop_id"],
				"email" => $fData["email"],
				"pass" => $fData["pass"],
				"pseudo" => $fData["pseudo"],
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"date_creation" => $fData["date_creation"],
				"mobile" => $fData["mobile"],
				"phone" => $fData["phone"],
				"phone_prefix" => $fData["phone_prefix"],
				"newsletter" => $fData["newsletter"],
				"gender" => $fData["gender"],
				"birthday" => $fData["birthday"],
				"active_hash" => $fData["active_hash"],
				"active" => $fData["active"],

            ], '', $ric);
            $form->addNotification("Le/la user a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_user", [
				"shop_id" => $fData["shop_id"],
				"email" => $fData["email"],
				"pass" => $fData["pass"],
				"pseudo" => $fData["pseudo"],
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"date_creation" => $fData["date_creation"],
				"mobile" => $fData["mobile"],
				"phone" => $fData["phone"],
				"phone_prefix" => $fData["phone_prefix"],
				"newsletter" => $fData["newsletter"],
				"gender" => $fData["gender"],
				"birthday" => $fData["birthday"],
				"active_hash" => $fData["active_hash"],
				"active" => $fData["active"],

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
                    "link" => E::link("NullosAdmin_Ekom_Generated_AppUserInfo_List") . "?s&user_id=$id",
                    "text" => "Voir les user infos",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EesEstimate_List") . "?s&user_id=$id",
                    "text" => "Voir les estimates",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkInvoice_List") . "?s&user_id=$id",
                    "text" => "Voir les invoices",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkOrder_List") . "?s&user_id=$id",
                    "text" => "Voir les orders",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkPasswordRecoveryRequest_List") . "?s&user_id=$id",
                    "text" => "Voir les password recovery requests",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductComment_List") . "?s&user_id=$id",
                    "text" => "Voir les product comments",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserHasAddress_List") . "?s&user_id=$id",
                    "text" => "Voir les user-addresses",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserHasProduct_List") . "?s&user_id=$id",
                    "text" => "Voir les user-products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserHasUserGroup_List") . "?s&user_id=$id",
                    "text" => "Voir les user-user groups",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_PeiWalletCard_List") . "?s&user_id=$id",
                    "text" => "Voir les wallet cards",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
