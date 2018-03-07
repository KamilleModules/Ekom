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


$choice_tax_group_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_tax_group", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'product_card_id',
];
$product_card_id = MorphicHelper::getFormContextValue("product_card_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
        
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
    'title' => "Product card has tax group",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_card_has_tax_group")
        ->addControl(SokoInputControl::create()
            ->setName("product_card_id")
            ->setLabel("Product card id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($product_card_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("tax_group_id")
            ->setLabel('Tax group id')
            ->setChoices($choice_tax_group_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_product_card_has_tax_group"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $product_card_id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product_card_has_tax_group", [
				"shop_id" => $fData["shop_id"],
				"product_card_id" => $fData["product_card_id"],
				"tax_group_id" => $fData["tax_group_id"],

            ]);
            $form->addNotification("Le/la Product card has tax group pour le/la product card \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product_card_has_tax_group", [
				"shop_id" => $fData["shop_id"],
				"tax_group_id" => $fData["tax_group_id"],

            ], [
				["product_card_id", "=", $product_card_id],
            
            ]);
            $form->addNotification("Le/la Product card has tax group pour le/la product card \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


