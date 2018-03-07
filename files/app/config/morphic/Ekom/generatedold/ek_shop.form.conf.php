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


$choice_lang_id = QuickPdo::fetchAll("select id, concat(id, \". \", label) as label from kamille.ek_lang", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_currency_id = QuickPdo::fetchAll("select id, concat(id, \". \", iso_code) as label from kamille.ek_currency", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);
$choice_timezone_id = QuickPdo::fetchAll("select id, concat(id, \". \", name) as label from kamille.ek_timezone", [], \PDO::FETCH_COLUMN|\PDO::FETCH_UNIQUE);


//--------------------------------------------
// SIMPLE FORM PATTERN
//--------------------------------------------
$ric=[
    'id',
];
$id = (array_key_exists('id', $_GET)) ? $_GET['id'] : null;
        
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
    'title' => "Shop",
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
            ->setValue($id)
        )
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
            ->setLabel('Lang id')
            ->setChoices($choice_lang_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("currency_id")
            ->setLabel('Currency id')
            ->setChoices($choice_currency_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
        ->addControl(SokoInputControl::create()
            ->setName("base_currency_id")
            ->setLabel("Base_currency_id")
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("timezone_id")
            ->setLabel('Timezone id')
            ->setChoices($choice_timezone_id)
            ->setProperties([
                'readonly' => $isUpdate,
            ])
            
        )
    ,        
    'feed' => MorphicHelper::getFeedFunction("ek_shop"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $id) {

        

        if (false === $isUpdate) {
            QuickPdo::insert("ek_shop", [
				"label" => $fData["label"],
				"host" => $fData["host"],
				"lang_id" => $fData["lang_id"],
				"currency_id" => $fData["currency_id"],
				"base_currency_id" => $fData["base_currency_id"],
				"timezone_id" => $fData["timezone_id"],

            ]);
            $form->addNotification("Le/la Shop a bien été ajouté(e)", "success");
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
            $form->addNotification("Le/la Shop a bien été mis(e) à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => $ric,
    //--------------------------------------------
    // IF HAS CONTEXT
    //--------------------------------------------
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasAddress_List") . "?shop_id=$id",
                    "text" => "Voir les addresses de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasCarrier_List") . "?shop_id=$id",
                    "text" => "Voir les carriers de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasCurrency_List") . "?shop_id=$id",
                    "text" => "Voir les currencies de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasLang_List") . "?shop_id=$id",
                    "text" => "Voir les langs de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasPaymentMethod_List") . "?shop_id=$id",
                    "text" => "Voir les payment methods de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProduct_List") . "?shop_id=$id",
                    "text" => "Voir les products de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductCard_List") . "?shop_id=$id",
                    "text" => "Voir les product cards de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List") . "?",
                    "text" => "Voir les product card langs de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductHasProvider_List") . "?",
                    "text" => "Voir les providers de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductHasTag_List") . "?",
                    "text" => "Voir les tags de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductLang_List") . "?",
                    "text" => "Voir les product langs de ce/cette Shop",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],
];


