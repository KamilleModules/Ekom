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

$choice_category_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_category", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_card_id = QuickPdo::fetchAll("select id, concat(id, \". \", id) as label from ek_product_card", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'category_id',
    'product_card_id',
];

$category_id = (array_key_exists("category_id", $_GET)) ? $_GET['category_id'] : null;
$product_card_id = (array_key_exists("product_card_id", $_GET)) ? $_GET['product_card_id'] : null;



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
    'title' => "category-product card",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_category_has_product_card")
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("category_id")
            ->setLabel("Category id")
            ->setProperties([
                'readonly' => (null !== $category_id),
            ])
            ->setValue($category_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.category",
            ]))         )
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("product_card_id")
            ->setLabel("Product card id")
            ->setProperties([
                'readonly' => (null !== $product_card_id),
            ])
            ->setValue($product_card_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product_card",
            ]))         ),
    'feed' => MorphicHelper::getFeedFunction("ek_category_has_product_card"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $category_id, $product_card_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_category_has_product_card", [
				"category_id" => $fData["category_id"],
				"product_card_id" => $fData["product_card_id"],

            ], '', $ric);
            $form->addNotification("Le/la category-product card a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_category_has_product_card", [

            ], [
				["category_id", "=", $category_id],
				["product_card_id", "=", $product_card_id],
            
            ]);
            $form->addNotification("Le/la category-product card a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
