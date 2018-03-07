<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\ManufacturerLayer;
use Module\Ekom\Api\Layer\ProductTypeLayer;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Api\Object\ShopHasProduct;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");
$id = (array_key_exists('product_id', $_GET)) ? (int)$_GET['product_id'] : 0;


$productControl = SokoAutocompleteInputControl::create()
    ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
        'action' => "auto.product",
    ]))
    ->setName("product_id")
    ->setLabel('Product id');
if (0 !== $id) {
    $productControl
        ->setProperties([
            'readonly' => true,
        ])
        ->setValue($id);
}


$sellers = SellerLayer::getItems($shopId);
$productTypes = ProductTypeLayer::getItems($shopId);
$manufacturers = ManufacturerLayer::getItems($shopId);
array_unshift($manufacturers, "Aucun");

$form = SokoForm::create()
    ->setName("soko-form-shop_has_product")
    ->addControl(SokoInputControl::create()
        ->setName("shop_id")
        ->setLabel('Shop id')
        ->setProperties([
            'readonly' => true,
        ])
        ->setValue($shopId)
    )
    ->addControl($productControl)
    ->addControl(SokoInputControl::create()
        ->setName("price")
        ->setLabel('Price')
    )
    ->addControl(SokoInputControl::create()
        ->setName("wholesale_price")
        ->setLabel('Wholesale price')
    )
    ->addControl(SokoInputControl::create()
        ->setName("quantity")
        ->setLabel('Quantity')
    )
    ->addControl(SokoBooleanChoiceControl::create()
        ->setName("active")
        ->setLabel('Active')
        ->setValue(1)
    )
    ->addControl(SokoChoiceControl::create()
        ->setName("seller_id")
        ->setLabel('Seller id')
        ->setChoices($sellers)
    )
    ->addControl(SokoChoiceControl::create()
        ->setName("product_type_id")
        ->setLabel('Product type id')
        ->setChoices($productTypes)
    )
    ->addControl(SokoChoiceControl::create()
        ->setName("manufacturer_id")
        ->setLabel('Manufacturer id')
        ->setChoices($manufacturers)
    )
    ->addControl(SokoInputControl::create()
        ->setName("reference")
        ->setLabel('Reference')
    )
    ->addControl(SokoInputControl::create()
        ->setName("codes")
        ->setLabel('Codes')
        ->setType("textarea")
    )
    ->addControl(SokoInputControl::create()
        ->setName("ean")
        ->setLabel('Ean')
    )
    ->addControl(SokoInputControl::create()
        ->setName("_discount_badge")
        ->setLabel('Discount badge (cache)')
    )
    ->addControl(SokoInputControl::create()
        ->setName("_popularity")
        ->setLabel('Popularity (cache)')
    )
    ->addValidationRule("product_id", SokoNotEmptyValidationRule::create());


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Shop has Product",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($shopId, $id) {

        if (0 === (int)$fData['manufacturer_id']) {
            $fData['manufacturer_id'] = null;
        }
        if (0 === $id) {
            ShopHasProduct::getInst()->create($fData);
            $form->addNotification("Le produit a bien été ajouté", "success");
        } else {


            ShopHasProduct::getInst()->update($fData, [
                "shop_id" => $shopId,
                "product_id" => $id,
            ]);
            $form->addNotification("Le produit a bien été mis à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
//        'shop_id',
        'product_id',
    ],
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [
                [
                    "link" => E::link("NullosAdmin_Ekom_ShopHasProductLang_List") . "?id=$id",
                    "text" => "Voir les traductions pour ce produit",
                ],
                [
                    "link" => E::link("NullosAdmin_Ekom_ShopHasProductTag_List") . "?id=$id",
                    "text" => "Voir les tags pour ce produit",
                ],
                [
                    "link" => E::link("NullosAdmin_Ekom_ShopHasProductProvider_List") . "?id=$id",
                    "text" => "Voir les fournisseurs pour ce produit",
                ],
            ],
        ],
    ],
];