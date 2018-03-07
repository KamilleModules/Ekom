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
    'title' => "Product comment",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_comment")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))    
            ->setName("product_id")
            ->setLabel("Product id")
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.user",
            ]))    
            ->setName("user_id")
            ->setLabel("User id")
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("date")
            ->setLabel("Date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("rating")
            ->setLabel("Rating")
        )
        ->addControl(SokoInputControl::create()
            ->setName("useful_counter")
            ->setLabel("Useful_counter")
        )
        ->addControl(SokoInputControl::create()
            ->setName("title")
            ->setLabel("Title")
        )
        ->addControl(SokoInputControl::create()
            ->setName("comment")
            ->setLabel("Comment")
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("active")
            ->setLabel("Active")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_product_comment"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product_comment", [
				"shop_id" => $fData["shop_id"],
				"product_id" => $fData["product_id"],
				"user_id" => $fData["user_id"],
				"date" => $fData["date"],
				"rating" => $fData["rating"],
				"useful_counter" => $fData["useful_counter"],
				"title" => $fData["title"],
				"comment" => $fData["comment"],
				"active" => $fData["active"],

            ]);
            $form->addNotification("Le/la Product comment a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product_comment", [
				"shop_id" => $fData["shop_id"],
				"product_id" => $fData["product_id"],
				"user_id" => $fData["user_id"],
				"date" => $fData["date"],
				"rating" => $fData["rating"],
				"useful_counter" => $fData["useful_counter"],
				"title" => $fData["title"],
				"comment" => $fData["comment"],
				"active" => $fData["active"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Product comment a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


