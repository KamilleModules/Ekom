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
    'z1',
    'z2',
    'z3',
    'z4',
    'z5',
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
    'title' => "frais port france",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-z_frais_port_france")
        ->addControl(SokoInputControl::create()
            ->setName("max_kg")
            ->setLabel("Max_kg")
        )
        ->addControl(SokoInputControl::create()
            ->setName("z1")
            ->setLabel("Z1")
        )
        ->addControl(SokoInputControl::create()
            ->setName("z2")
            ->setLabel("Z2")
        )
        ->addControl(SokoInputControl::create()
            ->setName("z3")
            ->setLabel("Z3")
        )
        ->addControl(SokoInputControl::create()
            ->setName("z4")
            ->setLabel("Z4")
        )
        ->addControl(SokoInputControl::create()
            ->setName("z5")
            ->setLabel("Z5")
        ),
    'feed' => MorphicHelper::getFeedFunction("z_frais_port_france"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $max_kg, $z1, $z2, $z3, $z4, $z5) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("z_frais_port_france", [
				"max_kg" => $fData["max_kg"],
				"z1" => $fData["z1"],
				"z2" => $fData["z2"],
				"z3" => $fData["z3"],
				"z4" => $fData["z4"],
				"z5" => $fData["z5"],

            ], '', $ric);
            $form->addNotification("Le/la frais port france a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("z_frais_port_france", [
				"max_kg" => $fData["max_kg"],
				"z1" => $fData["z1"],
				"z2" => $fData["z2"],
				"z3" => $fData["z3"],
				"z4" => $fData["z4"],
				"z5" => $fData["z5"],

            ], [
				["max_kg", "=", $max_kg],
				["z1", "=", $z1],
				["z2", "=", $z2],
				["z3", "=", $z3],
				["z4", "=", $z4],
				["z5", "=", $z5],
            
            ]);
            $form->addNotification("Le/la frais port france a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
