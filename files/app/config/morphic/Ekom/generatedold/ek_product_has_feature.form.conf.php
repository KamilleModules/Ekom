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


$choice_feature_id = QuickPdo::fetchAll("select id, concat(id, \". \", id) as label from kamille.ek_feature", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_feature_value_id = QuickPdo::fetchAll("select id, concat(id, \". \", feature_id) as label from kamille.ek_feature_value", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'product_id',
    'feature_id',
];
$product_id = MorphicHelper::getFormContextValue("product_id", $context);
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$feature_id = (array_key_exists('feature_id', $_GET)) ? $_GET['feature_id'] : null;
        
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
    'title' => "Product has feature",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_has_feature")
        ->addControl(SokoInputControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($product_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("feature_id")
            ->setLabel('Feature id')
            ->setChoices($choice_feature_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
            ->setValue($feature_id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("feature_value_id")
            ->setLabel('Feature value id')
            ->setChoices($choice_feature_value_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("position")
            ->setLabel("Position")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("technical_description")
            ->setLabel("Technical_description")
            ->setType("textarea")
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_product_has_feature"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $avatar, $product_id, $feature_id) {

                    
        //--------------------------------------------
        // IF SHOP_ID
        //--------------------------------------------
        $fData['shop_id'] = EkomNullosUser::getEkomValue("shop_id");    

        if (false === $isUpdate) {
            QuickPdo::insert("ek_product_has_feature", [
				"product_id" => $fData["product_id"],
				"feature_id" => $fData["feature_id"],
				"shop_id" => $fData["shop_id"],
				"feature_value_id" => $fData["feature_value_id"],
				"position" => $fData["position"],
				"technical_description" => $fData["technical_description"],

            ]);
            $form->addNotification("Le/la Product has feature pour le/la product \"$avatar\" a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_product_has_feature", [
				"shop_id" => $fData["shop_id"],
				"feature_value_id" => $fData["feature_value_id"],
				"position" => $fData["position"],
				"technical_description" => $fData["technical_description"],

            ], [
				["product_id", "=", $product_id],
				["feature_id", "=", $feature_id],
            
            ]);
            $form->addNotification("Le/la Product has feature pour le/la product \"$avatar\" a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
];


