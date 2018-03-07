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

$choice_country_id = QuickPdo::fetchAll("select id, concat(id, \". \", iso_code) as label from ek_country", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_lang", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'country_id',
    'lang_id',
];

$country_id = (array_key_exists("country_id", $_GET)) ? $_GET['country_id'] : null;
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
    'title' => "country lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_country_lang")
        ->addControl(SokoChoiceControl::create()
            ->setName("country_id")
            ->setLabel("Country id")
            ->setProperties([
                'readonly' => (null !== $country_id),
            ])
            ->setValue($country_id)
            ->setChoices($choice_country_id))
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
    'feed' => MorphicHelper::getFeedFunction("ek_country_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $country_id, $lang_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_country_lang", [
				"country_id" => $fData["country_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],

            ], '', $ric);
            $form->addNotification("Le/la country lang a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_country_lang", [
				"label" => $fData["label"],

            ], [
				["country_id", "=", $country_id],
				["lang_id", "=", $lang_id],
            
            ]);
            $form->addNotification("Le/la country lang a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
