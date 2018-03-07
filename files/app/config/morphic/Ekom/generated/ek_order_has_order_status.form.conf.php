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

$choice_order_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_order", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_order_status_id = QuickPdo::fetchAll("select id, concat(id, \". \", code) as label from ek_order_status", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$order_id = (array_key_exists("order_id", $_GET)) ? $_GET['order_id'] : null;
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
    'title' => "order-order status",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_order_has_order_status")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("order_id")
            ->setLabel("Order id")
            ->setProperties([
                'readonly' => (null !== $order_id),
            ])
            ->setValue($order_id)
            ->setChoices($choice_order_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("order_status_id")
            ->setLabel("Order status id")
            ->setProperties([
                'readonly' => (null !== $order_status_id),
            ])
            ->setValue($order_status_id)
            ->setChoices($choice_order_status_id))
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date")
            ->setLabel("Date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("extra")
            ->setLabel("Extra")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_order_has_order_status"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_order_has_order_status", [
				"order_id" => $fData["order_id"],
				"order_status_id" => $fData["order_status_id"],
				"date" => $fData["date"],
				"extra" => $fData["extra"],

            ], '', $ric);
            $form->addNotification("Le/la order-order status a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_order_has_order_status", [
				"order_id" => $fData["order_id"],
				"order_status_id" => $fData["order_status_id"],
				"date" => $fData["date"],
				"extra" => $fData["extra"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la order-order status a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
