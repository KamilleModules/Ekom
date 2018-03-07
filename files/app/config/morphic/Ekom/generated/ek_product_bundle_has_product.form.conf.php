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

$choice_product_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_product", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_bundle_id = QuickPdo::fetchAll("select id, concat(id, \". \", shop_id) as label from ek_product_bundle", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'product_bundle_id',
    'product_id',
];

$product_id = (array_key_exists("product_id", $_GET)) ? $_GET['product_id'] : null;
$product_bundle_id = (array_key_exists("product_bundle_id", $_GET)) ? $_GET['product_bundle_id'] : null;



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
    'title' => "product bundle-product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_bundle_has_product")
        ->addControl(SokoChoiceControl::create()
            ->setName("product_bundle_id")
            ->setLabel("Product bundle id")
            ->setProperties([
                'readonly' => (null !== $product_bundle_id),
            ])
            ->setValue($product_bundle_id)
            ->setChoices($choice_product_bundle_id))
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
        ->addControl(SokoInputControl::create()
            ->setName("quantity")
            ->setLabel("Quantity")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_product_bundle_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $product_bundle_id, $product_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_product_bundle_has_product", [
				"product_bundle_id" => $fData["product_bundle_id"],
				"product_id" => $fData["product_id"],
				"quantity" => $fData["quantity"],

            ], '', $ric);
            $form->addNotification("Le/la product bundle-product a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_product_bundle_has_product", [
				"quantity" => $fData["quantity"],

            ], [
				["product_bundle_id", "=", $product_bundle_id],
				["product_id", "=", $product_id],
            
            ]);
            $form->addNotification("Le/la product bundle-product a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
