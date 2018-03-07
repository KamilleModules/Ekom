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
    'discount_id',
    'lang_id',
];
$discount_id = (array_key_exists('discount_id', $_GET)) ? $_GET['discount_id'] : null;
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
    'title' => "Discount lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_discount_lang")
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.discount",
            ]))    
            ->setName("discount_id")
            ->setLabel("Discount id")
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
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_discount_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $discount_id, $lang_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_discount_lang", [
				"discount_id" => $fData["discount_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],

            ]);
            $form->addNotification("Le/la Discount lang a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_discount_lang", [
				"label" => $fData["label"],

            ], [
				["discount_id", "=", $discount_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la Discount lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


