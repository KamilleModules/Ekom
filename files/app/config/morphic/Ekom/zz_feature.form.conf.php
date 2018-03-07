<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Layer\UserGroupLayer;
use Module\Ekom\Api\Object\Feature;
use Module\Ekom\Api\Object\ProductCard;
use Module\Ekom\Api\Object\UserHasAddress;
use Module\Ekom\Api\Object\UserHasUserGroup;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoBooleanChoiceControl;
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
                "link" => E::link("NullosAdmin_Ekom_FeatureLang_List") . "?id=$id",
                "text" => "Voir les traductions pour cette caractéristique de produit",
            ],
        ],
    ];
}



$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Feature",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-feature")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel('Id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($id)
        )
//        ->addValidationRule("name", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_feature"),
    'process' => function ($fData, SokoFormInterface $form) use ($id) {
        if (null === $id) {
            Feature::getInst()->create($fData);
            $form->addNotification("La caractéristique de produit a bien été ajoutée", "success");
        } else {
            Feature::getInst()->update($fData, [
                "id" => $fData['id'],
            ]);
            $form->addNotification("La caractéristique de produit a bien été mise à jour", "success");
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




