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

// inferred data (can be overridden by fkeys)
$shop_id = EkomNullosUser::getEkomValue("shop_id");
$lang_id = EkomNullosUser::getEkomValue("lang_id");
$currency_id = EkomNullosUser::getEkomValue("currency_id");

$choice_feature_id = QuickPdo::fetchAll("select id, concat(id, \". \", id) as label from ek_feature", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_product", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_feature_value_id = QuickPdo::fetchAll("select id, concat(id, \". \", feature_id) as label from ek_feature_value", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'product_id',
    'feature_id',
    'shop_id',
];

$feature_id = (array_key_exists("feature_id", $_GET)) ? $_GET['feature_id'] : null;
$product_id = (array_key_exists("product_id", $_GET)) ? $_GET['product_id'] : null;
$feature_value_id = (array_key_exists("feature_value_id", $_GET)) ? $_GET['feature_value_id'] : null;
$shop_id = (array_key_exists("shop_id", $_GET)) ? $_GET['shop_id'] : $shop_id; // inferred



$avatar = (array_key_exists("avatar", $context)) ? $context['avatar'] : null;

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
    'title' => "product-feature",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_has_feature")
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => (null !== $product_id),
            ])
            ->setValue($product_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product",
            ]))         )
        ->addControl(SokoChoiceControl::create()
            ->setName("feature_id")
            ->setLabel("Feature id")
            ->setProperties([
                'readonly' => (null !== $feature_id),
            ])
            ->setValue($feature_id)
            ->setChoices($choice_feature_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("feature_value_id")
            ->setLabel("Feature value id")
            ->setProperties([
                'readonly' => (null !== $feature_value_id),
            ])
            ->setValue($feature_value_id)
            ->setChoices($choice_feature_value_id))
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("position")
            ->setLabel("Position")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("technical_description")
            ->setLabel("Technical_description")
            ->setType("textarea")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_product_has_feature"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $product_id, $feature_id, $shop_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_product_has_feature", [
				"product_id" => $fData["product_id"],
				"feature_id" => $fData["feature_id"],
				"shop_id" => $fData["shop_id"],
				"feature_value_id" => $fData["feature_value_id"],
				"position" => $fData["position"],
				"technical_description" => $fData["technical_description"],

            ], '', $ric);
            $form->addNotification("Le/la product-feature a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_product_has_feature", [
				"feature_value_id" => $fData["feature_value_id"],
				"position" => $fData["position"],
				"technical_description" => $fData["technical_description"],

            ], [
				["product_id", "=", $product_id],
				["feature_id", "=", $feature_id],
				["shop_id", "=", $shop_id],
            
            ]);
            $form->addNotification("Le/la product-feature a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
