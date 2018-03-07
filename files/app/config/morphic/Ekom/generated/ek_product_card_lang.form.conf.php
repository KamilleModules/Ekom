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
$choice_product_card_id = QuickPdo::fetchAll("select id, concat(id, \". \", id) as label from ek_product_card", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'product_card_id',
    'lang_id',
];

$lang_id = (array_key_exists("lang_id", $_GET)) ? $_GET['lang_id'] : $lang_id; // inferred
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
    'title' => "product card lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_card_lang")
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
    'feed' => MorphicHelper::getFeedFunction("ek_product_card_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $product_card_id, $lang_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_product_card_lang", [
				"product_card_id" => $fData["product_card_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],
				"description" => $fData["description"],
				"slug" => $fData["slug"],
				"meta_title" => $fData["meta_title"],
				"meta_description" => $fData["meta_description"],
				"meta_keywords" => $fData["meta_keywords"],

            ], '', $ric);
            $form->addNotification("Le/la product card lang a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_product_card_lang", [
				"label" => $fData["label"],
				"description" => $fData["description"],
				"slug" => $fData["slug"],
				"meta_title" => $fData["meta_title"],
				"meta_description" => $fData["meta_description"],
				"meta_keywords" => $fData["meta_keywords"],

            ], [
				["product_card_id", "=", $product_card_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la product card lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
