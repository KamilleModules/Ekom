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
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$address_id = (array_key_exists("address_id", $_GET)) ? $_GET['address_id'] : null;
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
    'title' => "shop-address",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_address")
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
            ->setName("type")
            ->setLabel("Type")
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_address"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_shop_has_address", [
				"shop_id" => $fData["shop_id"],
				"address_id" => $fData["address_id"],
				"type" => $fData["type"],
				"order" => $fData["order"],

            ], '', $ric);
            $form->addNotification("Le/la shop-address a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_shop_has_address", [
				"shop_id" => $fData["shop_id"],
				"address_id" => $fData["address_id"],
				"type" => $fData["type"],
				"order" => $fData["order"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la shop-address a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
