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

$choice_presenter_id = QuickPdo::fetchAll("select id, concat(id, \". \", first_name) as label from ekev_presenter", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_presenter_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ekev_presenter_group", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'presenter_group_id',
    'presenter_id',
];

$presenter_id = (array_key_exists("presenter_id", $_GET)) ? $_GET['presenter_id'] : null;
$presenter_group_id = (array_key_exists("presenter_group_id", $_GET)) ? $_GET['presenter_group_id'] : null;



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
    'title' => "presenter group-presenter",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_presenter_group_has_presenter")
        ->addControl(SokoChoiceControl::create()
            ->setName("presenter_group_id")
            ->setLabel("Presenter group id")
            ->setProperties([
                'readonly' => (null !== $presenter_group_id),
            ])
            ->setValue($presenter_group_id)
            ->setChoices($choice_presenter_group_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("presenter_id")
            ->setLabel("Presenter id")
            ->setProperties([
                'readonly' => (null !== $presenter_id),
            ])
            ->setValue($presenter_id)
            ->setChoices($choice_presenter_id)),
    'feed' => MorphicHelper::getFeedFunction("ekev_presenter_group_has_presenter"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $presenter_group_id, $presenter_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ekev_presenter_group_has_presenter", [
				"presenter_group_id" => $fData["presenter_group_id"],
				"presenter_id" => $fData["presenter_id"],

            ], '', $ric);
            $form->addNotification("Le/la presenter group-presenter a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ekev_presenter_group_has_presenter", [

            ], [
				["presenter_group_id", "=", $presenter_group_id],
				["presenter_id", "=", $presenter_id],
            
            ]);
            $form->addNotification("Le/la presenter group-presenter a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
