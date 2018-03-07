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


$choice_tax_id = QuickPdo::fetchAll("select id, concat(id, \". \", amount) as label from kamille.ek_tax", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'tax_group_id',
    'tax_id',
];
$tax_group_id = MorphicHelper::getFormContextValue("tax_group_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$tax_id = (array_key_exists('tax_id', $_GET)) ? $_GET['tax_id'] : null;
        
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
    'title' => "Tax group has tax",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_tax_group_has_tax")
        ->addControl(SokoInputControl::create()
            ->setName("tax_group_id")
            ->setLabel("Tax group id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($tax_group_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("tax_id")
            ->setLabel('Tax id')
            ->setChoices($choice_tax_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($tax_id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        )
        ->addControl(SokoInputControl::create()
            ->setName("mode")
            ->setLabel("Mode")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_tax_group_has_tax"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $tax_group_id, $tax_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_tax_group_has_tax", [
				"tax_group_id" => $fData["tax_group_id"],
				"tax_id" => $fData["tax_id"],
				"order" => $fData["order"],
				"mode" => $fData["mode"],

            ]);
            $form->addNotification("Le/la Tax group has tax pour le/la tax group \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_tax_group_has_tax", [
				"order" => $fData["order"],
				"mode" => $fData["mode"],

            ], [
				["tax_group_id", "=", $tax_group_id],
				["tax_id", "=", $tax_id],
            
            ]);
            $form->addNotification("Le/la Tax group has tax pour le/la tax group \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


