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

$choice_address_id = QuickPdo::fetchAll("select id, concat(id, \". \", first_name) as label from ek_address", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_user_id = QuickPdo::fetchAll("select id, concat(id, \". \", email) as label from ek_user", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'user_id',
    'address_id',
];

$address_id = (array_key_exists("address_id", $_GET)) ? $_GET['address_id'] : null;
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
    'title' => "user-address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_user_has_address")
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
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("address_id")
            ->setLabel("Address id")
            ->setProperties([
                'readonly' => (null !== $address_id),
            ])
            ->setValue($address_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.address",
            ]))         )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("order")
            ->setLabel("Order")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default_shipping_address")
            ->setLabel("Is_default_shipping_address")
            ->setValue(1)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("is_default_billing_address")
            ->setLabel("Is_default_billing_address")
            ->setValue(1)
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_user_has_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $user_id, $address_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_user_has_address", [
				"user_id" => $fData["user_id"],
				"address_id" => $fData["address_id"],
				"order" => $fData["order"],
				"is_default_shipping_address" => $fData["is_default_shipping_address"],
				"is_default_billing_address" => $fData["is_default_billing_address"],

            ], '', $ric);
            $form->addNotification("Le/la user-address a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_user_has_address", [
				"order" => $fData["order"],
				"is_default_shipping_address" => $fData["is_default_shipping_address"],
				"is_default_billing_address" => $fData["is_default_billing_address"],

            ], [
				["user_id", "=", $user_id],
				["address_id", "=", $address_id],
            
            ]);
            $form->addNotification("Le/la user-address a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
