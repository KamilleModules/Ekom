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
    'reference',
    'date',
    'location',
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
    'title' => "formations",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-tmp_formations")
        ->addControl(SokoInputControl::create()
            ->setName("reference")
            ->setLabel("Reference")
        )
        ->addControl(SokoInputControl::create()
            ->setName("location")
            ->setLabel("Location")
        ),
    'feed' => MorphicHelper::getFeedFunction("tmp_formations"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $reference, $date, $location) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("tmp_formations", [
				"reference" => $fData["reference"],
				"date" => $fData["date"],
				"location" => $fData["location"],

            ], '', $ric);
            $form->addNotification("Le/la formations a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("tmp_formations", [
				"reference" => $fData["reference"],
				"date" => $fData["date"],
				"location" => $fData["location"],

            ], [
				["reference", "=", $reference],
				["date", "=", $date],
				["location", "=", $location],
            
            ]);
            $form->addNotification("Le/la formations a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
