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
$choice_tax_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_tax_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'shop_id',
    'product_card_id',
];

$product_card_id = (array_key_exists("product_card_id", $_GET)) ? $_GET['product_card_id'] : null;
$tax_group_id = (array_key_exists("tax_group_id", $_GET)) ? $_GET['tax_group_id'] : null;
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
    'title' => "product card-tax group",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_card_has_tax_group")
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("product_card_id")
            ->setLabel("Product card id")
            ->setProperties([
                'readonly' => (null !== $product_card_id),
            ])
            ->setValue($product_card_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product_card",
            ]))         )
        ->addControl(SokoChoiceControl::create()
            ->setName("tax_group_id")
            ->setLabel("Tax group id")
            ->setProperties([
                'readonly' => (null !== $tax_group_id),
            ])
            ->setValue($tax_group_id)
            ->setChoices($choice_tax_group_id)),
    'feed' => MorphicHelper::getFeedFunction("ek_product_card_has_tax_group"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $shop_id, $product_card_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_product_card_has_tax_group", [
				"shop_id" => $fData["shop_id"],
				"product_card_id" => $fData["product_card_id"],
				"tax_group_id" => $fData["tax_group_id"],

            ], '', $ric);
            $form->addNotification("Le/la product card-tax group a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_product_card_has_tax_group", [
				"tax_group_id" => $fData["tax_group_id"],

            ], [
				["shop_id", "=", $shop_id],
				["product_card_id", "=", $product_card_id],
            
            ]);
            $form->addNotification("Le/la product card-tax group a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
