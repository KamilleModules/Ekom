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

$choice_product_card_id = QuickPdo::fetchAll("select id, concat(id, \". \", id) as label from ek_product_card", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$product_card_id = (array_key_exists("product_card_id", $_GET)) ? $_GET['product_card_id'] : null;



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
    'title' => "product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoInputControl::create()
            ->setName("reference")
            ->setLabel("Reference")
        )
        ->addControl(SokoInputControl::create()
            ->setName("weight")
            ->setLabel("Weight")
        )
        ->addControl(SokoInputControl::create()
            ->setName("price")
            ->setLabel("Price")
        )
        ->addControl(SokoAutocompleteInputControl::create()
            ->setName("product_card_id")
            ->setLabel("Product card id")
            ->setProperties([
                'readonly' => (null !== $product_card_id),
            ])
            ->setValue($product_card_id)
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.product_card",
            ]))         )
        ->addControl(SokoInputControl::create()
            ->setName("width")
            ->setLabel("Width")
        )
        ->addControl(SokoInputControl::create()
            ->setName("height")
            ->setLabel("Height")
        )
        ->addControl(SokoInputControl::create()
            ->setName("depth")
            ->setLabel("Depth")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_product", [
				"reference" => $fData["reference"],
				"weight" => $fData["weight"],
				"price" => $fData["price"],
				"product_card_id" => $fData["product_card_id"],
				"width" => $fData["width"],
				"height" => $fData["height"],
				"depth" => $fData["depth"],

            ], '', $ric);
            $form->addNotification("Le/la product a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_product", [
				"reference" => $fData["reference"],
				"weight" => $fData["weight"],
				"price" => $fData["price"],
				"product_card_id" => $fData["product_card_id"],
				"width" => $fData["width"],
				"height" => $fData["height"],
				"depth" => $fData["depth"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la product a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EccProductCardCombination_List") . "?s&product_id=$id",
                    "text" => "Voir les product card combinations",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EccProductCardCombinationHasProductCard_List") . "?s&product_id=$id",
                    "text" => "Voir les product card combination-product cards",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductBundleHasProduct_List") . "?s&product_id=$id",
                    "text" => "Voir les product bundle-products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductComment_List") . "?s&product_id=$id",
                    "text" => "Voir les product comments",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List") . "?s&product_id=$id",
                    "text" => "Voir les product group-products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasDiscount_List") . "?s&product_id=$id",
                    "text" => "Voir les product-discounts",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasFeature_List") . "?s&product_id=$id",
                    "text" => "Voir les product-features",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List") . "?s&product_id=$id",
                    "text" => "Voir les product-product attributes",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductLang_List") . "?s&product_id=$id",
                    "text" => "Voir les product langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProduct_List") . "?s&product_id=$id",
                    "text" => "Voir les shop-products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductCard_List") . "?s&product_id=$id",
                    "text" => "Voir les shop-product cards",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserHasProduct_List") . "?s&product_id=$id",
                    "text" => "Voir les user-products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkfsProduct_List") . "?s&product_id=$id",
                    "text" => "Voir les products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraEvent_List") . "?s&product_id=$id",
                    "text" => "Voir les events",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraTraining_List") . "?s&product_id=$id",
                    "text" => "Voir les trainings",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
