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


$choice_product_attribute_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_product_attribute", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ek_lang", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'product_attribute_id',
    'lang_id',
];
$product_attribute_id = (array_key_exists('product_attribute_id', $_GET)) ? $_GET['product_attribute_id'] : null;
$lang_id = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;
        
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
    'title' => "Product attribute lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_attribute_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("product_attribute_id")
            ->setLabel('Product attribute id')
            ->setChoices($choice_product_attribute_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($product_attribute_id)
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
            ->setName("name")
            ->setLabel("Name")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_product_attribute_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $product_attribute_id, $lang_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product_attribute_lang", [
				"product_attribute_id" => $fData["product_attribute_id"],
				"lang_id" => $fData["lang_id"],
				"name" => $fData["name"],

            ]);
            $form->addNotification("Le/la Product attribute lang a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product_attribute_lang", [
				"name" => $fData["name"],

            ], [
				["product_attribute_id", "=", $product_attribute_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la Product attribute lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


