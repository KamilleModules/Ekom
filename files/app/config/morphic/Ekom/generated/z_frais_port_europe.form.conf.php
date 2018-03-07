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
    'max_kg',
    'BE',
    'LU',
    'CH',
    'EURZ1',
    'EURZ2',
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
    'title' => "frais port europe",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-z_frais_port_europe")
        ->addControl(SokoInputControl::create()
            ->setName("max_kg")
            ->setLabel("Max_kg")
        )
        ->addControl(SokoInputControl::create()
            ->setName("BE")
            ->setLabel("BE")
        )
        ->addControl(SokoInputControl::create()
            ->setName("LU")
            ->setLabel("LU")
        )
        ->addControl(SokoInputControl::create()
            ->setName("CH")
            ->setLabel("CH")
        )
        ->addControl(SokoInputControl::create()
            ->setName("EURZ1")
            ->setLabel("EURZ1")
        )
        ->addControl(SokoInputControl::create()
            ->setName("EURZ2")
            ->setLabel("EURZ2")
        ),
    'feed' => MorphicHelper::getFeedFunction("z_frais_port_europe"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $max_kg, $BE, $LU, $CH, $EURZ1, $EURZ2) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("z_frais_port_europe", [
				"max_kg" => $fData["max_kg"],
				"BE" => $fData["BE"],
				"LU" => $fData["LU"],
				"CH" => $fData["CH"],
				"EURZ1" => $fData["EURZ1"],
				"EURZ2" => $fData["EURZ2"],

            ], '', $ric);
            $form->addNotification("Le/la frais port europe a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("z_frais_port_europe", [
				"max_kg" => $fData["max_kg"],
				"BE" => $fData["BE"],
				"LU" => $fData["LU"],
				"CH" => $fData["CH"],
				"EURZ1" => $fData["EURZ1"],
				"EURZ2" => $fData["EURZ2"],

            ], [
				["max_kg", "=", $max_kg],
				["BE", "=", $BE],
				["LU", "=", $LU],
				["CH", "=", $CH],
				["EURZ1", "=", $EURZ1],
				["EURZ2", "=", $EURZ2],
            
            ]);
            $form->addNotification("Le/la frais port europe a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
