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

$choice_discount_id = QuickPdo::fetchAll("select id, concat(id, \". \", type) as label from ek_discount", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_id = QuickPdo::fetchAll("select id, concat(id, \". \", reference) as label from ek_product", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'product_id',
    'discount_id',
];

$discount_id = (array_key_exists("discount_id", $_GET)) ? $_GET['discount_id'] : null;
$product_id = (array_key_exists("product_id", $_GET)) ? $_GET['product_id'] : null;



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
    'title' => "product-discount",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_has_discount")
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
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("discount_id")
            ->setLabel("Discount id")
            ->setProperties([
                'readonly' => (null !== $discount_id),
            ])
            ->setValue($discount_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.discount",
            ]))         )
        ->addControl(SokoInputControl::create()
            ->setName("conditions")
            ->setLabel("Conditions")
            ->setType("textarea")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_product_has_discount"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $product_id, $discount_id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_product_has_discount", [
				"product_id" => $fData["product_id"],
				"discount_id" => $fData["discount_id"],
				"conditions" => $fData["conditions"],
				"active" => $fData["active"],

            ], '', $ric);
            $form->addNotification("Le/la product-discount a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_product_has_discount", [
				"conditions" => $fData["conditions"],
				"active" => $fData["active"],

            ], [
				["product_id", "=", $product_id],
				["discount_id", "=", $discount_id],
            
            ]);
            $form->addNotification("Le/la product-discount a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,        
];
