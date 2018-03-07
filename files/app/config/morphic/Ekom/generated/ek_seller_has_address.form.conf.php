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
$choice_seller_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_seller", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'seller_id',
    'address_id',
];

$address_id = (array_key_exists("address_id", $_GET)) ? $_GET['address_id'] : null;
$seller_id = (array_key_exists("seller_id", $_GET)) ? $_GET['seller_id'] : null;



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
    'title' => "seller-address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_seller_has_address")
        ->addControl(SokoChoiceControl::create()
            ->setName("seller_id")
            ->setLabel("Seller id")
            ->setProperties([
                'readonly' => (null !== $seller_id),
            ])
            ->setValue($seller_id)
            ->setChoices($choice_seller_id))
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
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_seller_has_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $seller_id, $address_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_seller_has_address", [
				"seller_id" => $fData["seller_id"],
				"address_id" => $fData["address_id"],
				"order" => $fData["order"],

            ], '', $ric);
            $form->addNotification("Le/la seller-address a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_seller_has_address", [
				"order" => $fData["order"],

            ], [
				["seller_id", "=", $seller_id],
				["address_id", "=", $address_id],
            
            ]);
            $form->addNotification("Le/la seller-address a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
