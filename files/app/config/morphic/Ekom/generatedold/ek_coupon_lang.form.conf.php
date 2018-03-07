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


$choice_coupon_id = QuickPdo::fetchAll("select id, concat(id, \". \", code) as label from kamille.ek_coupon", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ek_lang", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'lang_id',
    'coupon_id',
];
$lang_id = (array_key_exists('lang_id', $_GET)) ? $_GET['lang_id'] : null;
$coupon_id = (array_key_exists('coupon_id', $_GET)) ? $_GET['coupon_id'] : null;
        
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
    'title' => "Coupon lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_coupon_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("coupon_id")
            ->setLabel('Coupon id')
            ->setChoices($choice_coupon_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($coupon_id)
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
    'feed' => MorphicHelper::getFeedFunction("ek_coupon_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $lang_id, $coupon_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_coupon_lang", [
				"coupon_id" => $fData["coupon_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],

            ]);
            $form->addNotification("Le/la Coupon lang a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_coupon_lang", [
				"label" => $fData["label"],

            ], [
				["coupon_id", "=", $coupon_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la Coupon lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


