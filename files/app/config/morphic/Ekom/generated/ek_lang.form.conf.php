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




$ric = [
    'id',
];

$id = (array_key_exists("id", $_GET)) ? $_GET['id'] : null;



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
    'title' => "lang",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_lang")
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
            ->setName("iso_code")
            ->setLabel("Iso_code")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_lang", [
				"label" => $fData["label"],
				"iso_code" => $fData["iso_code"],

            ], '', $ric);
            $form->addNotification("Le/la lang a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_lang", [
				"label" => $fData["label"],
				"iso_code" => $fData["iso_code"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la lang a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCartDiscountLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les cart discount langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCategoryLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les category langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCountryLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les country langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCouponLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les coupon langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkDiscountLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les discount langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkFeatureLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les feature langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkFeatureValueLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les feature value langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkOrderStatusLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les order status langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductAttributeLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les product attribute langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les product attribute value langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductCardLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les product card langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les product langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShop_List") . "?s&lang_id=$id",
                    "text" => "Voir les shops",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les shop-langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les shop-product card langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les shop-product langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkTag_List") . "?s&lang_id=$id",
                    "text" => "Voir les tags",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkTaxLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les tax langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevCourseLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les course langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkevEventLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les event langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkfsProduct_List") . "?s&lang_id=$id",
                    "text" => "Voir les products",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraCardLang_List") . "?s&lang_id=$id",
                    "text" => "Voir les card langs",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
