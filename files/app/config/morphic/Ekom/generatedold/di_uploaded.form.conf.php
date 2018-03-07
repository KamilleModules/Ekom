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
    'title' => "Uploaded",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_uploaded")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("path")
            ->setLabel("Path")
            ->setType("textarea")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date_upload")
            ->setLabel("Date_upload")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("ip")
            ->setLabel("Ip")
        )
        ->addControl(SokoInputControl::create()
            ->setName("http_user_agent")
            ->setLabel("Http_user_agent")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("di_uploaded"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("di_uploaded", [
				"path" => $fData["path"],
				"content" => $fData["content"],
				"date_upload" => $fData["date_upload"],
				"ip" => $fData["ip"],
				"http_user_agent" => $fData["http_user_agent"],

            ]);
            $form->addNotification("Le/la Uploaded a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("di_uploaded", [
				"path" => $fData["path"],
				"content" => $fData["content"],
				"date_upload" => $fData["date_upload"],
				"ip" => $fData["ip"],
				"http_user_agent" => $fData["http_user_agent"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Uploaded a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


