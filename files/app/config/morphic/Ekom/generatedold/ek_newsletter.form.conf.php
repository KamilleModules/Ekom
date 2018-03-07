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
    'id',
];
$id = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
        
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
    'title' => "Newsletter",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_newsletter")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("email")
            ->setLabel("Email")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("subscribe_date")
            ->setLabel("Subscribe_date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("unsubscribe_date")
            ->setLabel("Unsubscribe_date")
            ->addProperties([
                "required" => false,                       
            ])
                        
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_newsletter"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_newsletter", [
				"email" => $fData["email"],
				"subscribe_date" => $fData["subscribe_date"],
				"unsubscribe_date" => $fData["unsubscribe_date"],
				"active" => $fData["active"],

            ]);
            $form->addNotification("Le/la Newsletter a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_newsletter", [
				"email" => $fData["email"],
				"subscribe_date" => $fData["subscribe_date"],
				"unsubscribe_date" => $fData["unsubscribe_date"],
				"active" => $fData["active"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Newsletter a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


