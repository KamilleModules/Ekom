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

$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_lang", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_order_status_id = QuickPdo::fetchAll("select id, concat(id, \". \", code) as label from ek_order_status", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'order_status_id',
    'lang_id',
];

$lang_id = (array_key_exists("lang_id", $_GET)) ? $_GET['lang_id'] : $lang_id; // inferred
$order_status_id = (array_key_exists("order_status_id", $_GET)) ? $_GET['order_status_id'] : null;



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
    'title' => "order status lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_order_status_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("order_status_id")
            ->setLabel("Order status id")
            ->setProperties([
                'readonly' => (null !== $order_status_id),
            ])
            ->setValue($order_status_id)
            ->setChoices($choice_order_status_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel("Lang id")
            ->setProperties([
                'readonly' => (null !== $lang_id),
            ])
            ->setValue($lang_id)
            ->setChoices($choice_lang_id))
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel("Label")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_order_status_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $order_status_id, $lang_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_order_status_lang", [
				"order_status_id" => $fData["order_status_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],

            ], '', $ric);
            $form->addNotification("Le/la order status lang a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_order_status_lang", [
				"label" => $fData["label"],

            ], [
				["order_status_id", "=", $order_status_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la order status lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
