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

$choice_course_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ekev_course", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_lang", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'course_id',
    'lang_id',
];

$course_id = (array_key_exists("course_id", $_GET)) ? $_GET['course_id'] : null;
$lang_id = (array_key_exists("lang_id", $_GET)) ? $_GET['lang_id'] : $lang_id; // inferred



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
    'title' => "course lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_course_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("course_id")
            ->setLabel("Course id")
            ->setProperties([
                'readonly' => (null !== $course_id),
            ])
            ->setValue($course_id)
            ->setChoices($choice_course_id))
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
        ),
    'feed' => MorphicHelper::getFeedFunction("ekev_course_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $course_id, $lang_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ekev_course_lang", [
				"course_id" => $fData["course_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],

            ], '', $ric);
            $form->addNotification("Le/la course lang a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ekev_course_lang", [
				"label" => $fData["label"],

            ], [
				["course_id", "=", $course_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la course lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
