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


$choice_mail_id = QuickPdo::fetchAll("select id, concat(id, \". \", type) as label from kamille.fm_mail", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "Mail link",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-fm_mail_link")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("mail_id")
            ->setLabel('Mail id')
            ->setChoices($choice_mail_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoInputControl::create()
            ->setName("link_name")
            ->setLabel("Link_name")
        )
        ->addControl(SokoInputControl::create()
            ->setName("route")
            ->setLabel("Route")
        )
        ->addControl(SokoInputControl::create()
            ->setName("route_params")
            ->setLabel("Route_params")
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("hash")
            ->setLabel("Hash")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("fm_mail_link"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("fm_mail_link", [
				"mail_id" => $fData["mail_id"],
				"link_name" => $fData["link_name"],
				"route" => $fData["route"],
				"route_params" => $fData["route_params"],
				"hash" => $fData["hash"],

            ]);
            $form->addNotification("Le/la Mail link a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("fm_mail_link", [
				"mail_id" => $fData["mail_id"],
				"link_name" => $fData["link_name"],
				"route" => $fData["route"],
				"route_params" => $fData["route_params"],
				"hash" => $fData["hash"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Mail link a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


