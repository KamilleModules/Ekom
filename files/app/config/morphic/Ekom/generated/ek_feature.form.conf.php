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
    'title' => "feature",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-ek_feature")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel("Id")
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)),
    'feed' => MorphicHelper::getFeedFunction("ek_feature"),
    'process' => function ($fData, SokoFormInterface $form) use ($isUpdate, $ric, $id) {
            
        if (false === $isUpdate) {
            $ric = QuickPdo::insert("ek_feature", [

            ], '', $ric);
            $form->addNotification("Le/la feature a bien été ajouté(e)", "success");
            
            MorphicHelper::redirectToUpdateFormIfNecessary($ric);
            
        } else {
            QuickPdo::update("ek_feature", [

            ], [
				["id", "=", $id],
            
            ]);
            $form->addNotification("Le/la feature a bien été mis(e) à jour", "success");
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
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkFeatureLang_List") . "?s&feature_id=$id",
                    "text" => "Voir les feature langs",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkFeatureValue_List") . "?s&feature_id=$id",
                    "text" => "Voir les feature values",
                    "disabled" => !$isUpdate,
                ],

                [
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductHasFeature_List") . "?s&feature_id=$id",
                    "text" => "Voir les product-features",
                    "disabled" => !$isUpdate,
                ],

            ],
        ],
    ],        
];
