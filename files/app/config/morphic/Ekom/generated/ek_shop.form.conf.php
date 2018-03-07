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

$choice_currency_id = QuickPdo::fetchAll("select id, concat(id, \". \", iso_code) as label from ek_currency", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from ek_lang", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
$choice_timezone_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from ek_timezone", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);



$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;
$currency_id = (array_key_exists("currency_id", $_GET)) ? $_GET['currency_id'] : $currency_id; // inferred
$lang_id = (array_key_exists("lang_id", $_GET)) ? $_GET['lang_id'] : $lang_id; // inferred
$timezone_id = (array_key_exists("timezone_id", $_GET)) ? $_GET['timezone_id'] : null;



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
    'title' => "shop",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_shop")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel("Label")
        )
        ->addControl(SokoInputControl::create()
            ->setName("host")
            ->setLabel("Host")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel("Lang id")
            ->setProperties([
                'readonly' => (null !== $lang_id),
            ])
            ->setValue($lang_id)
            ->setChoices($choice_lang_id))
        ->addControl(SokoChoiceControl::create()
            ->setName("currency_id")
            ->setLabel("Currency id")
            ->setProperties([
                'readonly' => (null !== $currency_id),
            ])
            ->setValue($currency_id)
            ->setChoices($choice_currency_id))
        ->addControl(SokoInputControl::create()
            ->setName("base_currency_id")
            ->setLabel("Base_currency_id")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("timezone_id")
            ->setLabel("Timezone id")
            ->setProperties([
                'readonly' => (null !== $timezone_id),
            ])
            ->setValue($timezone_id)
            ->setChoices($choice_timezone_id)),
    'feed' => MorphicHelper::getFeedFunction("ek_shop"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_shop", [
				"label" => $fData["label"],
				"host" => $fData["host"],
				"lang_id" => $fData["lang_id"],
				"currency_id" => $fData["currency_id"],
				"base_currency_id" => $fData["base_currency_id"],
				"timezone_id" => $fData["timezone_id"],

            ], '', $ric);
            $form->addNotification("Le/la shop a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_shop", [
				"label" => $fData["label"],
				"host" => $fData["host"],
				"lang_id" => $fData["lang_id"],
				"currency_id" => $fData["currency_id"],
				"base_currency_id" => $fData["base_currency_id"],
				"timezone_id" => $fData["timezone_id"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la shop a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EccProductCardCombination_List") . "?s&shop_id=$id",
                    "text" => "Voir les product card combinations",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCartDiscount_List") . "?s&shop_id=$id",
                    "text" => "Voir les cart discounts",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCategory_List") . "?s&shop_id=$id",
                    "text" => "Voir les categories",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCoupon_List") . "?s&shop_id=$id",
                    "text" => "Voir les coupons",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkDiscount_List") . "?s&shop_id=$id",
                    "text" => "Voir les discounts",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkInvoice_List") . "?s&shop_id=$id",
                    "text" => "Voir les invoices",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkManufacturer_List") . "?s&shop_id=$id",
                    "text" => "Voir les manufacturers",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkOrder_List") . "?s&shop_id=$id",
                    "text" => "Voir les orders",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkOrderStatus_List") . "?s&shop_id=$id",
                    "text" => "Voir les order statuses",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductBundle_List") . "?s&shop_id=$id",
                    "text" => "Voir les product bundles",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductCardHasTaxGroup_List") . "?s&shop_id=$id",
                    "text" => "Voir les product card-tax groups",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductComment_List") . "?s&shop_id=$id",
                    "text" => "Voir les product comments",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductGroup_List") . "?s&shop_id=$id",
                    "text" => "Voir les product groups",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasFeature_List") . "?s&shop_id=$id",
                    "text" => "Voir les product-features",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductType_List") . "?s&shop_id=$id",
                    "text" => "Voir les product types",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProvider_List") . "?s&shop_id=$id",
                    "text" => "Voir les providers",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkSeller_List") . "?s&shop_id=$id",
                    "text" => "Voir les sellers",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopConfiguration_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop configurations",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasAddress_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop-addresses",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasCarrier_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop-carriers",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasCurrency_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop-currencies",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasLang_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop-langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasPaymentMethod_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop-payment methods",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProduct_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop-products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductCard_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop-product cards",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkTaxGroup_List") . "?s&shop_id=$id",
                    "text" => "Voir les tax groups",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUser_List") . "?s&shop_id=$id",
                    "text" => "Voir les users",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUserGroup_List") . "?s&shop_id=$id",
                    "text" => "Voir les user groups",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevCourse_List") . "?s&shop_id=$id",
                    "text" => "Voir les courses",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEvent_List") . "?s&shop_id=$id",
                    "text" => "Voir les events",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevHotel_List") . "?s&shop_id=$id",
                    "text" => "Voir les hotels",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevLocation_List") . "?s&shop_id=$id",
                    "text" => "Voir les locations",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevPresenter_List") . "?s&shop_id=$id",
                    "text" => "Voir les presenters",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevPresenterGroup_List") . "?s&shop_id=$id",
                    "text" => "Voir les presenter groups",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevShopProductCardEvent_List") . "?s&shop_id=$id",
                    "text" => "Voir les shop product card events",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkfsProduct_List") . "?s&shop_id=$id",
                    "text" => "Voir les products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraCard_List") . "?s&shop_id=$id",
                    "text" => "Voir les cards",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraDateRange_List") . "?s&shop_id=$id",
                    "text" => "Voir les date ranges",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraLocation_List") . "?s&shop_id=$id",
                    "text" => "Voir les locations",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraTrainer_List") . "?s&shop_id=$id",
                    "text" => "Voir les trainers",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraTrainerGroup_List") . "?s&shop_id=$id",
                    "text" => "Voir les trainer groups",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraTraining_List") . "?s&shop_id=$id",
                    "text" => "Voir les trainings",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
