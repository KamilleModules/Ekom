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
    'title' => "Category",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_category")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoInputControl::create()
            ->setName("name")
            ->setLabel("Name")
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.category",
            ]))    
            ->setName("category_id")
            ->setLabel("Category id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("order")
            ->setLabel("Order")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_category"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_category", [
				"name" => $fData["name"],
				"category_id" => $fData["category_id"],
				"shop_id" => $fData["shop_id"],
				"order" => $fData["order"],

            ]);
            $form->addNotification("Le/la Category a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_category", [
				"name" => $fData["name"],
				"category_id" => $fData["category_id"],
				"shop_id" => $fData["shop_id"],
				"order" => $fData["order"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Category a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCategoryHasDiscount_List") . "?category_id=$id",
                    "text" => "Voir les discounts de ce/cette Category",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List") . "?category_id=$id",
                    "text" => "Voir les product cards de ce/cette Category",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


