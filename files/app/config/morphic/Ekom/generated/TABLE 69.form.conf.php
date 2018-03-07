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




$ric = [
    'IMAGE_FORMATION',
    'NOM_FORMATION',
    'DESCRIPTIF_FORMATION',
    'PRE_REQUIS',
    'INFOS_FORMATION',
    'POUR_QUI',
    'VALIDATION',
    'DUREE_FORMATION',
];




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
    'title' => "table 69",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-TABLE 69")
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
        ),
    'feed' => MorphicHelper::getFeedFunction("TABLE 69"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $IMAGE_FORMATION, $NOM_FORMATION, $DESCRIPTIF_FORMATION, $PRE_REQUIS, $INFOS_FORMATION, $POUR_QUI, $VALIDATION, $DUREE_FORMATION) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("TABLE 69", [
				"IMAGE_FORMATION" => $fData["IMAGE_FORMATION"],
				"NOM_FORMATION" => $fData["NOM_FORMATION"],
				"DESCRIPTIF_FORMATION" => $fData["DESCRIPTIF_FORMATION"],
				"PRE_REQUIS" => $fData["PRE_REQUIS"],
				"INFOS_FORMATION" => $fData["INFOS_FORMATION"],
				"POUR_QUI" => $fData["POUR_QUI"],
				"VALIDATION" => $fData["VALIDATION"],
				"DUREE_FORMATION" => $fData["DUREE_FORMATION"],

            ], '', $ric);
            $form->addNotification("Le/la table 69 a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
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
            $form->addNotification("Le/la table 69 a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
