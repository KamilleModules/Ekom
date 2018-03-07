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

$choice_country_id = QuickPdo::fetchAll("select id, concat(id, \". \", iso_code) as label from ek_country", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$country_id = (array_key_exists("country_id", $_GET)) ? $_GET['country_id'] : null;



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
    'title' => "address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_address")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoInputControl::create()
            ->setName("first_name")
            ->setLabel("First_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("last_name")
            ->setLabel("Last_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone")
            ->setLabel("Phone")
        )
        ->addControl(SokoInputControl::create()
            ->setName("phone_prefix")
            ->setLabel("Phone_prefix")
        )
        ->addControl(SokoInputControl::create()
            ->setName("address")
            ->setLabel("Address")
        )
        ->addControl(SokoInputControl::create()
            ->setName("city")
            ->setLabel("City")
        )
        ->addControl(SokoInputControl::create()
            ->setName("postcode")
            ->setLabel("Postcode")
        )
        ->addControl(SokoInputControl::create()
            ->setName("supplement")
            ->setLabel("Supplement")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("country_id")
            ->setLabel("Country id")
            ->setProperties([
                'readonly' => (null !== $country_id),
            ])
            ->setValue($country_id)
            ->setChoices($choice_country_id)),
    'feed' => MorphicHelper::getFeedFunction("ek_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_address", [
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"phone" => $fData["phone"],
				"phone_prefix" => $fData["phone_prefix"],
				"address" => $fData["address"],
				"city" => $fData["city"],
				"postcode" => $fData["postcode"],
				"supplement" => $fData["supplement"],
				"active" => $fData["active"],
				"country_id" => $fData["country_id"],

            ], '', $ric);
            $form->addNotification("Le/la address a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_address", [
				"first_name" => $fData["first_name"],
				"last_name" => $fData["last_name"],
				"phone" => $fData["phone"],
				"phone_prefix" => $fData["phone_prefix"],
				"address" => $fData["address"],
				"city" => $fData["city"],
				"postcode" => $fData["postcode"],
				"supplement" => $fData["supplement"],
				"active" => $fData["active"],
				"country_id" => $fData["country_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la address a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkSellerHasAddress_List") . "?s&address_id=$id",
                    "text" => "Voir les seller-addresses",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasAddress_List") . "?s&address_id=$id",
                    "text" => "Voir les shop-addresses",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserHasAddress_List") . "?s&address_id=$id",
                    "text" => "Voir les user-addresses",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
