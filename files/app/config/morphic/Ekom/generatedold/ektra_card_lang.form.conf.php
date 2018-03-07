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


$choice_training_card_id = QuickPdo::fetchAll("select id, concat(id, \". \", shop_id) as label from kamille.ektra_card", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ek_lang", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'training_card_id',
    'lang_id',
];
$training_card_id = (array_key_exists('training_card_id', $_GET)) ? $_GET['training_card_id'] : null;
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
    'title' => "Card lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ektra_card_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("training_card_id")
            ->setLabel('Training card id')
            ->setChoices($choice_training_card_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($training_card_id)
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
            ->setName("prerequisites")
            ->setLabel("Prerequisites")
            ->setType("textarea")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ektra_card_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $training_card_id, $lang_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ektra_card_lang", [
				"training_card_id" => $fData["training_card_id"],
				"lang_id" => $fData["lang_id"],
				"prerequisites" => $fData["prerequisites"],

            ]);
            $form->addNotification("Le/la Card lang a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ektra_card_lang", [
				"prerequisites" => $fData["prerequisites"],

            ], [
				["training_card_id", "=", $training_card_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la Card lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


