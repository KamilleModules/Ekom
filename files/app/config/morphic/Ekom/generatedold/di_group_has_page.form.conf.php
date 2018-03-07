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


$choice_page_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.di_page", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'group_id',
    'page_id',
];
$group_id = MorphicHelper::getFormContextValue("group_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$page_id = (array_key_exists('page_id', $_GET)) ? $_GET['page_id'] : null;
        
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
    'title' => "Group has page",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_group_has_page")
        ->addControl(SokoInputControl::create()
            ->setName("group_id")
            ->setLabel("Group id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($group_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("page_id")
            ->setLabel('Page id')
            ->setChoices($choice_page_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($page_id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("position")
            ->setLabel("Position")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("di_group_has_page"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $group_id, $page_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("di_group_has_page", [
				"group_id" => $fData["group_id"],
				"page_id" => $fData["page_id"],
				"position" => $fData["position"],

            ]);
            $form->addNotification("Le/la Group has page pour le/la group \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("di_group_has_page", [
				"position" => $fData["position"],

            ], [
				["group_id", "=", $group_id],
				["page_id", "=", $page_id],
            
            ]);
            $form->addNotification("Le/la Group has page pour le/la group \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


