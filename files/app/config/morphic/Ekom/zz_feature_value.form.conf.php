<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\FeatureLayer;
use Module\Ekom\Api\Object\FeatureValue;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;


$id = (array_key_exists('id', $_GET)) ? (int)$_GET['id'] : null;



$afterElements = [];
if (null !== $id) {
    $afterElements[] = [
        "type" => "pivotLinks",
        "links" => [
            [
                "link" => E::link("NullosAdmin_Ekom_FeatureValueLang_List") . "?id=$id",
                "text" => "Voir les traductions pour cette valeur de caractéristique de produit",
            ],
        ],
    ];
}


$langId = EkomNullosUser::getEkomValue("lang_id");
$features = FeatureLayer::getItems($langId);



$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Feature value",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-feature_value")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel('Id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("feature_id")
            ->setLabel('Feature id')
            ->setChoices($features)
        )
//        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_feature_value"),
    'process' => function ($fData, SokoFormInterface $form) use ($id) {
        if (null === $id) {
            FeatureValue::getInst()->create($fData);
            $form->addNotification("La valeur de caractéristique de produit a bien été ajoutée", "success");
        } else {
            FeatureValue::getInst()->update($fData, [
                "id" => $fData['id'],
            ]);
            $form->addNotification("La valeur de caractéristique de produit a bien été mise à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        "id",
    ],
    'formAfterElements' => $afterElements,
];




