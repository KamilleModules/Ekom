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


$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ek_lang", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'lang_id',
    'product_id',
];
$lang_id = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;
$product_id = (array_key_exists('product_id', $_GET)) ? $_GET['product_id'] : null;
        
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
    'title' => "Shop has product lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_product_lang")
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))    
            ->setName("product_id")
            ->setLabel("Product id")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel('Lang id')
            ->setChoices($choice_lang_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($lang_id)
        )
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
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $lang_id, $product_id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_has_product_lang", [
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

            ]);
            $form->addNotification("Le/la Shop has product lang a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_has_product_lang", [
				"shop_id" => $fData["shop_id"],
				"label" => $fData["label"],
				"description" => $fData["description"],
				"slug" => $fData["slug"],
				"out_of_stock_text" => $fData["out_of_stock_text"],
				"meta_title" => $fData["meta_title"],
				"meta_description" => $fData["meta_description"],
				"meta_keywords" => $fData["meta_keywords"],

            ], [
				["product_id", "=", $product_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la Shop has product lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


