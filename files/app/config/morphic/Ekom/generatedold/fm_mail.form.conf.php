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
    'title' => "Mail",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-fm_mail")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("type")
            ->setLabel("Type")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_sent")
            ->setLabel("Date_sent")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("email")
            ->setLabel("Email")
        )
        ->addControl(SokoInputControl::create()
            ->setName("hash")
            ->setLabel("Hash")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("fm_mail"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("fm_mail", [
				"type" => $fData["type"],
				"date_sent" => $fData["date_sent"],
				"email" => $fData["email"],
				"hash" => $fData["hash"],
				"variables" => $fData["variables"],

            ]);
            $form->addNotification("Le/la Mail a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("fm_mail", [
				"type" => $fData["type"],
				"date_sent" => $fData["date_sent"],
				"email" => $fData["email"],
				"hash" => $fData["hash"],
				"variables" => $fData["variables"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Mail a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


