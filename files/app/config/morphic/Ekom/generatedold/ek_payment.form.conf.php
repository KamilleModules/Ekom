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


$choice_invoice_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ek_invoice", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "Payment",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_payment")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("invoice_id")
            ->setLabel('Invoice id')
            ->setChoices($choice_invoice_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date")
            ->setLabel("Date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("paid")
            ->setLabel("Paid")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("feedback_details")
            ->setLabel("Feedback_details")
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("amount")
            ->setLabel("Amount")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_payment"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_payment", [
				"invoice_id" => $fData["invoice_id"],
				"date" => $fData["date"],
				"paid" => $fData["paid"],
				"feedback_details" => $fData["feedback_details"],
				"amount" => $fData["amount"],

            ]);
            $form->addNotification("Le/la Payment a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_payment", [
				"invoice_id" => $fData["invoice_id"],
				"date" => $fData["date"],
				"paid" => $fData["paid"],
				"feedback_details" => $fData["feedback_details"],
				"amount" => $fData["amount"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Payment a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


