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

$choice_carrier_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_carrier", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'shop_id',
    'carrier_id',
];

$carrier_id = (array_key_exists("carrier_id", $_GET)) ? $_GET['carrier_id'] : null;
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
    'title' => "shop-carrier",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_carrier")
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("carrier_id")
            ->setLabel("Carrier id")
            ->setProperties([
                'readonly' => (null !== $carrier_id),
            ])
            ->setValue($carrier_id)
            ->setChoices($choice_carrier_id))
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("priority")
            ->setLabel("Priority")
            ->setValue(1)
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_carrier"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $shop_id, $carrier_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_shop_has_carrier", [
				"shop_id" => $fData["shop_id"],
				"carrier_id" => $fData["carrier_id"],
				"priority" => $fData["priority"],

            ], '', $ric);
            $form->addNotification("Le/la shop-carrier a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_shop_has_carrier", [
				"priority" => $fData["priority"],

            ], [
				["shop_id", "=", $shop_id],
				["carrier_id", "=", $carrier_id],
            
            ]);
            $form->addNotification("Le/la shop-carrier a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
