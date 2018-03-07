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


$choice_page_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.di_page", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "Element",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-di_element")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("page_id")
            ->setLabel('Page id')
            ->setChoices($choice_page_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoInputControl::create()
            ->setName("type")
            ->setLabel("Type")
        )
        ->addControl(SokoInputControl::create()
            ->setName("varname")
            ->setLabel("Varname")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pos_x")
            ->setLabel("Pos_x")
        )
        ->addControl(SokoInputControl::create()
            ->setName("pos_y")
            ->setLabel("Pos_y")
        )
        ->addControl(SokoInputControl::create()
            ->setName("width")
            ->setLabel("Width")
        )
        ->addControl(SokoInputControl::create()
            ->setName("height")
            ->setLabel("Height")
        )
        ->addControl(SokoInputControl::create()
            ->setName("validation")
            ->setLabel("Validation")
            ->setType("textarea")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("di_element"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("di_element", [
				"page_id" => $fData["page_id"],
				"type" => $fData["type"],
				"varname" => $fData["varname"],
				"pos_x" => $fData["pos_x"],
				"pos_y" => $fData["pos_y"],
				"width" => $fData["width"],
				"height" => $fData["height"],
				"validation" => $fData["validation"],

            ]);
            $form->addNotification("Le/la Element a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("di_element", [
				"page_id" => $fData["page_id"],
				"type" => $fData["type"],
				"varname" => $fData["varname"],
				"pos_x" => $fData["pos_x"],
				"pos_y" => $fData["pos_y"],
				"width" => $fData["width"],
				"height" => $fData["height"],
				"validation" => $fData["validation"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Element a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


