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


$choice_course_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ekev_course", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ek_lang", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'course_id',
    'lang_id',
];
$course_id = (array_key_exists('course_id', $_GET)) ? $_GET['course_id'] : null;
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
    'title' => "Course lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_course_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("course_id")
            ->setLabel('Course id')
            ->setChoices($choice_course_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($course_id)
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
    'feed' => MorphicHelper::getFeedFunction("ekev_course_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $course_id, $lang_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_course_lang", [
				"course_id" => $fData["course_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],

            ]);
            $form->addNotification("Le/la Course lang a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_course_lang", [
				"label" => $fData["label"],

            ], [
				["course_id", "=", $course_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la Course lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


