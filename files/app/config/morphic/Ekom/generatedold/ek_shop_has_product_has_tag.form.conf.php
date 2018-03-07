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
    'product_id',
    'tag_id',
];
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$product_id = (array_key_exists('product_id', $_GET)) ? $_GET['product_id'] : null;
$tag_id = (array_key_exists('tag_id', $_GET)) ? $_GET['tag_id'] : null;
        
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
    'title' => "Shop has product has tag",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_product_has_tag")
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
                'action' => "auto.tag",
            ]))    
            ->setName("tag_id")
            ->setLabel("Tag id")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product_has_tag"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $product_id, $tag_id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_has_product_has_tag", [
				"shop_id" => $fData["shop_id"],
				"product_id" => $fData["product_id"],
				"tag_id" => $fData["tag_id"],

            ]);
            $form->addNotification("Le/la Shop has product has tag pour le/la shop \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_has_product_has_tag", [
				"shop_id" => $fData["shop_id"],

            ], [
				["product_id", "=", $product_id],
				["tag_id", "=", $tag_id],
            
            ]);
            $form->addNotification("Le/la Shop has product has tag pour le/la shop \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


