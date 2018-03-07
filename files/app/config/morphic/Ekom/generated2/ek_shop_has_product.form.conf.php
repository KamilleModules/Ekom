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

$choice_manufacturer_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_manufacturer", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_product_type_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_product_type", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_seller_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_seller", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_shop_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_shop", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'shop_id',
    'product_id',
];

$manufacturer_id = (array_key_exists("manufacturer_id", $_GET)) ? $_GET['manufacturer_id'] : null;
$product_type_id = (array_key_exists("product_type_id", $_GET)) ? $_GET['product_type_id'] : null;
$seller_id = (array_key_exists("seller_id", $_GET)) ? $_GET['seller_id'] : null;
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
    'title' => "shop-product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop_has_product")
        ->addControl(SokoChoiceControl::create()
            ->setName("shop_id")
            ->setLabel("Shop id")
            ->setProperties([
                'readonly' => (null !== $shop_id),
            ])
            ->setValue($shop_id)
            ->setChoices($choice_shop_id))
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("product_id")
            ->setLabel("Product id")
            ->setProperties([
                'readonly' => (null !== $product_id),
            ])
            ->setValue($product_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product_id",
            ]))         )
        ->addControl(SokoInputControl::create()
            ->setName("price")
            ->setLabel("Price")
        )
        ->addControl(SokoInputControl::create()
            ->setName("wholesale_price")
            ->setLabel("Wholesale_price")
        )
        ->addControl(SokoInputControl::create()
            ->setName("quantity")
            ->setLabel("Quantity")
        )
        ->addControl(SokoBooleanChoiceControl::create()
            ->setName("active")
            ->setLabel("Active")
            ->setValue(1)
        )
        ->addControl(SokoInputControl::create()
            ->setName("_discount_badge")
            ->setLabel("_discount_badge")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("seller_id")
            ->setLabel("Seller id")
            ->setProperties([
                'readonly' => (null !== $seller_id),
            ])
            ->setValue($seller_id)
            ->setChoices($choice_seller_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("product_type_id")
            ->setLabel("Product type id")
            ->setProperties([
                'readonly' => (null !== $product_type_id),
            ])
            ->setValue($product_type_id)
            ->setChoices($choice_product_type_id))
        ->addControl(SokoInputControl::create()
            ->setName("reference")
            ->setLabel("Reference")
        )
        ->addControl(SokoInputControl::create()
            ->setName("_popularity")
            ->setLabel("_popularity")
        )
        ->addControl(SokoInputControl::create()
            ->setName("codes")
            ->setLabel("Codes")
            ->setType("textarea")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("manufacturer_id")
            ->setLabel("Manufacturer id")
            ->setProperties([
                'readonly' => (null !== $manufacturer_id),
            ])
            ->setValue($manufacturer_id)
            ->setChoices($choice_manufacturer_id))
        ->addControl(SokoInputControl::create()
            ->setName("ean")
            ->setLabel("Ean")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $shop_id, $product_id) {
            
        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop_has_product", [
				"shop_id" => $fData["shop_id"],
				"product_id" => $fData["product_id"],
				"price" => $fData["price"],
				"wholesale_price" => $fData["wholesale_price"],
				"quantity" => $fData["quantity"],
				"active" => $fData["active"],
				"_discount_badge" => $fData["_discount_badge"],
				"seller_id" => $fData["seller_id"],
				"product_type_id" => $fData["product_type_id"],
				"reference" => $fData["reference"],
				"_popularity" => $fData["_popularity"],
				"codes" => $fData["codes"],
				"manufacturer_id" => $fData["manufacturer_id"],
				"ean" => $fData["ean"],

            ]);
            $form->addNotification("Le/la shop-product a bien été ajouté(e)", "success");
        } else {
            QuickPdo::update("ek_shop_has_product", [
				"price" => $fData["price"],
				"wholesale_price" => $fData["wholesale_price"],
				"quantity" => $fData["quantity"],
				"active" => $fData["active"],
				"_discount_badge" => $fData["_discount_badge"],
				"seller_id" => $fData["seller_id"],
				"product_type_id" => $fData["product_type_id"],
				"reference" => $fData["reference"],
				"_popularity" => $fData["_popularity"],
				"codes" => $fData["codes"],
				"manufacturer_id" => $fData["manufacturer_id"],
				"ean" => $fData["ean"],

            ], [
				["shop_id", "=", $shop_id],
				["product_id", "=", $product_id],
            
            ]);
            $form->addNotification("Le/la shop-product a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,            
    //--------------------------------------------
    // CHILDREN
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductHasProvider_List") . "?shop_id=$shop_id&product_id=$product_id",
                    "text" => "shop-product-provider",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductHasTag_List") . "?shop_id=$shop_id&product_id=$product_id",
                    "text" => "shop-product-tag",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductLang_List") . "?shop_id=$shop_id&product_id=$product_id",
                    "text" => "shop-product lang",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
