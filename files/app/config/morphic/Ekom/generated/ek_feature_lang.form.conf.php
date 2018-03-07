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

$choice_feature_id = QuickPdo::fetchAll("select id, concat(id, \". \", id) as label from ek_feature", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_lang", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'lang_id',
    'feature_id',
];

$feature_id = (array_key_exists("feature_id", $_GET)) ? $_GET['feature_id'] : null;
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
    'title' => "feature lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_feature_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("feature_id")
            ->setLabel("Feature id")
            ->setProperties([
                'readonly' => (null !== $feature_id),
            ])
            ->setValue($feature_id)
            ->setChoices($choice_feature_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel("Lang id")
            ->setProperties([
                'readonly' => (null !== $lang_id),
            ])
            ->setValue($lang_id)
            ->setChoices($choice_lang_id))
        ->addControl(SokoInputControl::create()
            ->setName("name")
            ->setLabel("Name")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_feature_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $lang_id, $feature_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_feature_lang", [
				"feature_id" => $fData["feature_id"],
				"lang_id" => $fData["lang_id"],
				"name" => $fData["name"],

            ], '', $ric);
            $form->addNotification("Le/la feature lang a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_feature_lang", [
				"name" => $fData["name"],

            ], [
				["feature_id", "=", $feature_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la feature lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
