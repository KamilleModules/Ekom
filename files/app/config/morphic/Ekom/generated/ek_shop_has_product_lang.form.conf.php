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
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_product", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'lang_id',
    'product_id',
    'shop_id',
];

$lang_id = (array_key_exists("lang_id", $_GET)) ? $_GET['lang_id'] : $lang_id; // inferred
$shop_id = (array_key_exists("shop_id", $_GET)) ? $_GET['shop_id'] : $shop_id; // inferred
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
    'title' => "shop-product lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_product_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => (null !== $product_id),
            ])
            ->setValue($product_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))         )
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
        )
        ->addControl(SokoInputControl::create()
            ->setName("description")
            ->setLabel("Description")
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("slug")
            ->setLabel("Slug")
        )
        ->addControl(SokoInputControl::create()
            ->setName("out_of_stock_text")
            ->setLabel("Out_of_stock_text")
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_title")
            ->setLabel("Meta_title")
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_description")
            ->setLabel("Meta_description")
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_keywords")
            ->setLabel("Meta_keywords")
            ->setType("textarea")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $lang_id, $product_id, $shop_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_shop_has_product_lang", [
				"shop_id" => $fData["shop_id"],
				"product_id" => $fData["product_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],
				"description" => $fData["description"],
				"slug" => $fData["slug"],
				"out_of_stock_text" => $fData["out_of_stock_text"],
				"meta_title" => $fData["meta_title"],
				"meta_description" => $fData["meta_description"],
				"meta_keywords" => $fData["meta_keywords"],

            ], '', $ric);
            $form->addNotification("Le/la shop-product lang a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_shop_has_product_lang", [
				"label" => $fData["label"],
				"description" => $fData["description"],
				"slug" => $fData["slug"],
				"out_of_stock_text" => $fData["out_of_stock_text"],
				"meta_title" => $fData["meta_title"],
				"meta_description" => $fData["meta_description"],
				"meta_keywords" => $fData["meta_keywords"],

            ], [
				["shop_id", "=", $shop_id],
				["product_id", "=", $product_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la shop-product lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
