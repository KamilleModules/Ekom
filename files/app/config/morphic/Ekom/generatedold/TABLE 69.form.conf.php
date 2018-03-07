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




//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'IMAGE_FORMATION',
    'NOM_FORMATION',
    'DESCRIPTIF_FORMATION',
    'PRE_REQUIS',
    'INFOS_FORMATION',
    'POUR_QUI',
    'VALIDATION',
    'DUREE_FORMATION',
];
$IMAGE_FORMATION = (array_key_exists('IMAGE_FORMATION', $_GET)) ? $_GET['IMAGE_FORMATION'] : null;
$NOM_FORMATION = (array_key_exists('NOM_FORMATION', $_GET)) ? $_GET['NOM_FORMATION'] : null;
$DESCRIPTIF_FORMATION = (array_key_exists('DESCRIPTIF_FORMATION', $_GET)) ? $_GET['DESCRIPTIF_FORMATION'] : null;
$PRE_REQUIS = (array_key_exists('PRE_REQUIS', $_GET)) ? $_GET['PRE_REQUIS'] : null;
$INFOS_FORMATION = (array_key_exists('INFOS_FORMATION', $_GET)) ? $_GET['INFOS_FORMATION'] : null;
$POUR_QUI = (array_key_exists('POUR_QUI', $_GET)) ? $_GET['POUR_QUI'] : null;
$VALIDATION = (array_key_exists('VALIDATION', $_GET)) ? $_GET['VALIDATION'] : null;
$DUREE_FORMATION = (array_key_exists('DUREE_FORMATION', $_GET)) ? $_GET['DUREE_FORMATION'] : null;
        
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
    'title' => "TABLE 69",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-TABLE_69")
        ->addControl(SokoInputControl::create()
            ->setName("IMAGE_FORMATION")
            ->setLabel("IMAGE_FORMATION")
        )
        ->addControl(SokoInputControl::create()
            ->setName("NOM_FORMATION")
            ->setLabel("NOM_FORMATION")
        )
        ->addControl(SokoInputControl::create()
            ->setName("DESCRIPTIF_FORMATION")
            ->setLabel("DESCRIPTIF_FORMATION")
        )
        ->addControl(SokoInputControl::create()
            ->setName("PRE_REQUIS")
            ->setLabel("PRE_REQUIS")
        )
        ->addControl(SokoInputControl::create()
            ->setName("INFOS_FORMATION")
            ->setLabel("INFOS_FORMATION")
        )
        ->addControl(SokoInputControl::create()
            ->setName("POUR_QUI")
            ->setLabel("POUR_QUI")
        )
        ->addControl(SokoInputControl::create()
            ->setName("VALIDATION")
            ->setLabel("VALIDATION")
        )
        ->addControl(SokoInputControl::create()
            ->setName("DUREE_FORMATION")
            ->setLabel("DUREE_FORMATION")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("TABLE 69"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $IMAGE_FORMATION, $NOM_FORMATION, $DESCRIPTIF_FORMATION, $PRE_REQUIS, $INFOS_FORMATION, $POUR_QUI, $VALIDATION, $DUREE_FORMATION) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("TABLE 69", [
				"IMAGE_FORMATION" => $fData["IMAGE_FORMATION"],
				"NOM_FORMATION" => $fData["NOM_FORMATION"],
				"DESCRIPTIF_FORMATION" => $fData["DESCRIPTIF_FORMATION"],
				"PRE_REQUIS" => $fData["PRE_REQUIS"],
				"INFOS_FORMATION" => $fData["INFOS_FORMATION"],
				"POUR_QUI" => $fData["POUR_QUI"],
				"VALIDATION" => $fData["VALIDATION"],
				"DUREE_FORMATION" => $fData["DUREE_FORMATION"],

            ]);
            $form->addNotification("Le/la TABLE 69 a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("TABLE 69", [
				"IMAGE_FORMATION" => $fData["IMAGE_FORMATION"],
				"NOM_FORMATION" => $fData["NOM_FORMATION"],
				"DESCRIPTIF_FORMATION" => $fData["DESCRIPTIF_FORMATION"],
				"PRE_REQUIS" => $fData["PRE_REQUIS"],
				"INFOS_FORMATION" => $fData["INFOS_FORMATION"],
				"POUR_QUI" => $fData["POUR_QUI"],
				"VALIDATION" => $fData["VALIDATION"],
				"DUREE_FORMATION" => $fData["DUREE_FORMATION"],

            ], [
				["IMAGE_FORMATION", "=", $IMAGE_FORMATION],
				["NOM_FORMATION", "=", $NOM_FORMATION],
				["DESCRIPTIF_FORMATION", "=", $DESCRIPTIF_FORMATION],
				["PRE_REQUIS", "=", $PRE_REQUIS],
				["INFOS_FORMATION", "=", $INFOS_FORMATION],
				["POUR_QUI", "=", $POUR_QUI],
				["VALIDATION", "=", $VALIDATION],
				["DUREE_FORMATION", "=", $DUREE_FORMATION],
            
            ]);
            $form->addNotification("Le/la TABLE 69 a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


