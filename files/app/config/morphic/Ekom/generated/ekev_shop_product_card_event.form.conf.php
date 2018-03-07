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

$choice_product_card_id = QuickPdo::fetchAll("select id, concat(id, \". \", id) as label from ek_product_card", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_event_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ekev_event", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'shop_id',
    'event_id',
    'product_card_id',
];

$product_card_id = (array_key_exists("product_card_id", $_GET)) ? $_GET['product_card_id'] : null;
$shop_id = (array_key_exists("shop_id", $_GET)) ? $_GET['shop_id'] : $shop_id; // inferred
$event_id = (array_key_exists("event_id", $_GET)) ? $_GET['event_id'] : null;



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
    'title' => "shop product card event",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_shop_product_card_event")
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("event_id")
            ->setLabel("Event id")
            ->setProperties([
                'readonly' => (null !== $event_id),
            ])
            ->setValue($event_id)
            ->setChoices($choice_event_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("product_card_id")
            ->setLabel("Product card id")
            ->setProperties([
                'readonly' => (null !== $product_card_id),
            ])
            ->setValue($product_card_id)
            ->setChoices($choice_product_card_id)),
    'feed' => MorphicHelper::getFeedFunction("ekev_shop_product_card_event"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $shop_id, $event_id, $product_card_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ekev_shop_product_card_event", [
				"shop_id" => $fData["shop_id"],
				"event_id" => $fData["event_id"],
				"product_card_id" => $fData["product_card_id"],

            ], '', $ric);
            $form->addNotification("Le/la shop product card event a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ekev_shop_product_card_event", [

            ], [
				["shop_id", "=", $shop_id],
				["event_id", "=", $event_id],
				["product_card_id", "=", $product_card_id],
            
            ]);
            $form->addNotification("Le/la shop product card event a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
