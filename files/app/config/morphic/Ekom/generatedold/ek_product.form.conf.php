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
    'title' => "Product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("reference")
            ->setLabel("Reference")
        )
        ->addControl(SokoInputControl::create()
            ->setName("weight")
            ->setLabel("Weight")
        )
        ->addControl(SokoInputControl::create()
            ->setName("price")
            ->setLabel("Price")
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product_card",
            ]))    
            ->setName("product_card_id")
            ->setLabel("Product card id")
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
            ->setName("depth")
            ->setLabel("Depth")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product", [
				"reference" => $fData["reference"],
				"weight" => $fData["weight"],
				"price" => $fData["price"],
				"product_card_id" => $fData["product_card_id"],
				"width" => $fData["width"],
				"height" => $fData["height"],
				"depth" => $fData["depth"],

            ]);
            $form->addNotification("Le/la Product a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product", [
				"reference" => $fData["reference"],
				"weight" => $fData["weight"],
				"price" => $fData["price"],
				"product_card_id" => $fData["product_card_id"],
				"width" => $fData["width"],
				"height" => $fData["height"],
				"depth" => $fData["depth"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Product a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
    //--------------------------------------------
    // IF HAS CONTEXT
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasDiscount_List") . "?product_id=$id",
                    "text" => "Voir les discounts de ce/cette Product",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasFeature_List") . "?product_id=$id",
                    "text" => "Voir les features de ce/cette Product",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List") . "?product_id=$id",
                    "text" => "Voir les product attributes de ce/cette Product",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


