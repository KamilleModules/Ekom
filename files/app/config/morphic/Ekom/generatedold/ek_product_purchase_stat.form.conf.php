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
    'title' => "Product purchase stat",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_purchase_stat")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(EkomSokoDateControl::create()
            ->useDatetime()
            ->setName("purchase_date")
            ->setLabel("Purchase_date")
            ->addProperties([
                "required" => true,                       
            ])
                        
        )
        ->addControl(SokoInputControl::create()
            ->setName("shop_id")
            ->setLabel("Shop_id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("user_id")
            ->setLabel("User_id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("currency_id")
            ->setLabel("Currency_id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("product_id")
            ->setLabel("Product_id")
        )
        ->addControl(SokoInputControl::create()
            ->setName("product_ref")
            ->setLabel("Product_ref")
        )
        ->addControl(SokoInputControl::create()
            ->setName("product_label")
            ->setLabel("Product_label")
        )
        ->addControl(SokoInputControl::create()
            ->setName("quantity")
            ->setLabel("Quantity")
        )
        ->addControl(SokoInputControl::create()
            ->setName("price")
            ->setLabel("Price")
        )
        ->addControl(SokoInputControl::create()
            ->setName("price_without_tax")
            ->setLabel("Price_without_tax")
        )
        ->addControl(SokoInputControl::create()
            ->setName("total")
            ->setLabel("Total")
        )
        ->addControl(SokoInputControl::create()
            ->setName("total_without_tax")
            ->setLabel("Total_without_tax")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_product_purchase_stat"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product_purchase_stat", [
				"purchase_date" => $fData["purchase_date"],
				"shop_id" => $fData["shop_id"],
				"user_id" => $fData["user_id"],
				"currency_id" => $fData["currency_id"],
				"product_id" => $fData["product_id"],
				"product_ref" => $fData["product_ref"],
				"product_label" => $fData["product_label"],
				"quantity" => $fData["quantity"],
				"price" => $fData["price"],
				"price_without_tax" => $fData["price_without_tax"],
				"total" => $fData["total"],
				"total_without_tax" => $fData["total_without_tax"],
				"attribute_selection" => $fData["attribute_selection"],
				"product_details_selection" => $fData["product_details_selection"],

            ]);
            $form->addNotification("Le/la Product purchase stat a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product_purchase_stat", [
				"purchase_date" => $fData["purchase_date"],
				"shop_id" => $fData["shop_id"],
				"user_id" => $fData["user_id"],
				"currency_id" => $fData["currency_id"],
				"product_id" => $fData["product_id"],
				"product_ref" => $fData["product_ref"],
				"product_label" => $fData["product_label"],
				"quantity" => $fData["quantity"],
				"price" => $fData["price"],
				"price_without_tax" => $fData["price_without_tax"],
				"total" => $fData["total"],
				"total_without_tax" => $fData["total_without_tax"],
				"attribute_selection" => $fData["attribute_selection"],
				"product_details_selection" => $fData["product_details_selection"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Product purchase stat a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


