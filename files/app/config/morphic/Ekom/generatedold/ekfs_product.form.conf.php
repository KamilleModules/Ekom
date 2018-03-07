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


$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ek_lang", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


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
    'title' => "Ekfs product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ekfs_product")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel('Lang id')
            ->setChoices($choice_lang_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel("Label")
        )
        ->addControl(SokoInputControl::create()
            ->setName("ref")
            ->setLabel("Ref")
        )
        ->addControl(SokoInputControl::create()
            ->setName("sale_price_without_tax")
            ->setLabel("Sale_price_without_tax")
        )
        ->addControl(SokoInputControl::create()
            ->setName("sale_price_with_tax")
            ->setLabel("Sale_price_with_tax")
        )
        ->addControl(SokoInputControl::create()
            ->setName("attr_string")
            ->setLabel("Attr_string")
        )
        ->addControl(SokoInputControl::create()
            ->setName("uri_card")
            ->setLabel("Uri_card")
        )
        ->addControl(SokoInputControl::create()
            ->setName("uri_thumb")
            ->setLabel("Uri_thumb")
        )
        ->addControl(            
            SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))    
            ->setName("product_id")
            ->setLabel("Product id")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ekfs_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ekfs_product", [
				"shop_id" => $fData["shop_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],
				"ref" => $fData["ref"],
				"sale_price_without_tax" => $fData["sale_price_without_tax"],
				"sale_price_with_tax" => $fData["sale_price_with_tax"],
				"attr_string" => $fData["attr_string"],
				"uri_card" => $fData["uri_card"],
				"uri_thumb" => $fData["uri_thumb"],
				"product_id" => $fData["product_id"],

            ]);
            $form->addNotification("Le/la Ekfs product a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ekfs_product", [
				"shop_id" => $fData["shop_id"],
				"lang_id" => $fData["lang_id"],
				"label" => $fData["label"],
				"ref" => $fData["ref"],
				"sale_price_without_tax" => $fData["sale_price_without_tax"],
				"sale_price_with_tax" => $fData["sale_price_with_tax"],
				"attr_string" => $fData["attr_string"],
				"uri_card" => $fData["uri_card"],
				"uri_thumb" => $fData["uri_thumb"],
				"product_id" => $fData["product_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la Ekfs product a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


