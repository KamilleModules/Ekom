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

$choice_currency_id = QuickPdo::fetchAll("select id, concat(id, \". \", iso_code) as label from ek_currency", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'shop_id',
    'currency_id',
];

$currency_id = (array_key_exists("currency_id", $_GET)) ? $_GET['currency_id'] : $currency_id; // inferred
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
    'title' => "shop-currency",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_currency")
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("currency_id")
            ->setLabel("Currency id")
            ->setProperties([
                'readonly' => (null !== $currency_id),
            ])
            ->setValue($currency_id)
            ->setChoices($choice_currency_id))
        ->addControl(SokoInputControl::create()
            ->setName("exchange_rate")
            ->setLabel("Exchange_rate")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_currency"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $shop_id, $currency_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_shop_has_currency", [
				"shop_id" => $fData["shop_id"],
				"currency_id" => $fData["currency_id"],
				"exchange_rate" => $fData["exchange_rate"],
				"active" => $fData["active"],

            ], '', $ric);
            $form->addNotification("Le/la shop-currency a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_shop_has_currency", [
				"exchange_rate" => $fData["exchange_rate"],
				"active" => $fData["active"],

            ], [
				["shop_id", "=", $shop_id],
				["currency_id", "=", $currency_id],
            
            ]);
            $form->addNotification("Le/la shop-currency a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
