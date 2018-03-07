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

$choice_product_card_combination_id = QuickPdo::fetchAll("select id, concat(id, \". \", product_id) as label from ecc_product_card_combination", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_card_id = QuickPdo::fetchAll("select id, concat(id, \". \", id) as label from ek_product_card", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_product", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$product_card_combination_id = (array_key_exists("product_card_combination_id", $_GET)) ? $_GET['product_card_combination_id'] : null;
$product_card_id = (array_key_exists("product_card_id", $_GET)) ? $_GET['product_card_id'] : null;
$product_id = (array_key_exists("product_id", $_GET)) ? $_GET['product_id'] : null;



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
    'title' => "product card combination-product card",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ecc_product_card_combination_has_product_card")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoChoiceControl::create()
            ->setName("product_card_combination_id")
            ->setLabel("Product card combination id")
            ->setProperties([
                'readonly' => (null !== $product_card_combination_id),
            ])
            ->setValue($product_card_combination_id)
            ->setChoices($choice_product_card_combination_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("product_card_id")
            ->setLabel("Product card id")
            ->setProperties([
                'readonly' => (null !== $product_card_id),
            ])
            ->setValue($product_card_id)
            ->setChoices($choice_product_card_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => (null !== $product_id),
            ])
            ->setValue($product_id)
            ->setChoices($choice_product_id))
        ->addControl(SokoInputControl::create()
            ->setName("quantity")
            ->setLabel("Quantity")
        ),
    'feed' => MorphicHelper::getFeedFunction("ecc_product_card_combination_has_product_card"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ecc_product_card_combination_has_product_card", [
				"product_card_combination_id" => $fData["product_card_combination_id"],
				"product_card_id" => $fData["product_card_id"],
				"product_id" => $fData["product_id"],
				"quantity" => $fData["quantity"],

            ], '', $ric);
            $form->addNotification("Le/la product card combination-product card a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ecc_product_card_combination_has_product_card", [
				"product_card_combination_id" => $fData["product_card_combination_id"],
				"product_card_id" => $fData["product_card_id"],
				"product_id" => $fData["product_id"],
				"quantity" => $fData["quantity"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la product card combination-product card a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
