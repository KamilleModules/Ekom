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
    'category_id',
];
$lang_id = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;
$category_id = (array_key_exists('category_id', $_GET)) ? $_GET['category_id'] : null;
        
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
    'title' => "Category lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_category_lang")
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.category",
            ]))    
            ->setName("category_id")
            ->setLabel("Category id")
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
    'feed' => MorphicHelper::getFeedFunction("ek_category_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $lang_id, $category_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_category_lang", [
				"category_id" => $fData["category_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],
				"description" => $fData["description"],
				"slug" => $fData["slug"],
				"meta_title" => $fData["meta_title"],
				"meta_description" => $fData["meta_description"],
				"meta_keywords" => $fData["meta_keywords"],

            ]);
            $form->addNotification("Le/la Category lang a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_category_lang", [
				"label" => $fData["label"],
				"description" => $fData["description"],
				"slug" => $fData["slug"],
				"meta_title" => $fData["meta_title"],
				"meta_description" => $fData["meta_description"],
				"meta_keywords" => $fData["meta_keywords"],

            ], [
				["category_id", "=", $category_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la Category lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


