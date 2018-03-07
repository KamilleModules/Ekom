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


$choice_presenter_id = QuickPdo::fetchAll("select id, concat(id, \". \", first_name) as label from kamille.ekev_presenter", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'presenter_group_id',
    'presenter_id',
];
$presenter_group_id = MorphicHelper::getFormContextValue("presenter_group_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$presenter_id = (array_key_exists('presenter_id', $_GET)) ? $_GET['presenter_id'] : null;
        
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
    'title' => "Presenter group has presenter",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekev_presenter_group_has_presenter")
        ->addControl(SokoInputControl::create()
            ->setName("presenter_group_id")
            ->setLabel("Presenter group id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($presenter_group_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("presenter_id")
            ->setLabel('Presenter id')
            ->setChoices($choice_presenter_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($presenter_id)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ekev_presenter_group_has_presenter"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $presenter_group_id, $presenter_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ekev_presenter_group_has_presenter", [
				"presenter_group_id" => $fData["presenter_group_id"],
				"presenter_id" => $fData["presenter_id"],

            ]);
            $form->addNotification("Le/la Presenter group has presenter pour le/la presenter group \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekev_presenter_group_has_presenter", [

            ], [
				["presenter_group_id", "=", $presenter_group_id],
				["presenter_id", "=", $presenter_id],
            
            ]);
            $form->addNotification("Le/la Presenter group has presenter pour le/la presenter group \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


