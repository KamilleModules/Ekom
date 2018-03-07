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
    'title' => "product attribute value",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_product_attribute_value")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id))
        ->addControl(SokoInputControl::create()
            ->setName("value")
            ->setLabel("Value")
        ),
    'feed' => MorphicHelper::getFeedFunction("ek_product_attribute_value"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_product_attribute_value", [
				"value" => $fData["value"],

            ], '', $ric);
            $form->addNotification("Le/la product attribute value a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_product_attribute_value", [
				"value" => $fData["value"],

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la product attribute value a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List") . "?s&product_attribute_value_id=$id",
                    "text" => "Voir les product attribute value langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List") . "?s&product_attribute_value_id=$id",
                    "text" => "Voir les product-product attributes",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
