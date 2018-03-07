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


$choice_product_attribute_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_product_attribute", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_product_attribute_value_id = QuickPdo::fetchAll("select id, concat(id, \". \", value) as label from kamille.ek_product_attribute_value", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'product_id',
    'product_attribute_id',
    'product_attribute_value_id',
];
$product_id = MorphicHelper::getFormContextValue("product_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$product_attribute_id = (array_key_exists('product_attribute_id', $_GET)) ? $_GET['product_attribute_id'] : null;
$product_attribute_value_id = (array_key_exists('product_attribute_value_id', $_GET)) ? $_GET['product_attribute_value_id'] : null;
        
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
    'title' => "Product has product attribute",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_has_product_attribute")
        ->addControl(SokoInputControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($product_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("product_attribute_id")
            ->setLabel('Product attribute id')
            ->setChoices($choice_product_attribute_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($product_attribute_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("product_attribute_value_id")
            ->setLabel('Product attribute value id')
            ->setChoices($choice_product_attribute_value_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($product_attribute_value_id)
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("order")
            ->setLabel("Order")
            ->setValue(1)
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_product_has_product_attribute"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $product_id, $product_attribute_id, $product_attribute_value_id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product_has_product_attribute", [
				"product_id" => $fData["product_id"],
				"product_attribute_id" => $fData["product_attribute_id"],
				"product_attribute_value_id" => $fData["product_attribute_value_id"],
				"order" => $fData["order"],

            ]);
            $form->addNotification("Le/la Product has product attribute pour le/la product \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product_has_product_attribute", [
				"order" => $fData["order"],

            ], [
				["product_id", "=", $product_id],
				["product_attribute_id", "=", $product_attribute_id],
				["product_attribute_value_id", "=", $product_attribute_value_id],
            
            ]);
            $form->addNotification("Le/la Product has product attribute pour le/la product \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


