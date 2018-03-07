<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Layer\UserGroupLayer;
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
                "link" => E::link("NullosAdmin_Ekom_ProductCardLang_List") . "?id=$id",
                "text" => "Voir les traductions pour cette carte produit",
            ],
        ],
    ];
}



$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product card",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-product_card")
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
    'feed' => MorphicHelper::getFeedFunction("ek_product_card"),
    'process' => function ($fData, SokoFormInterface $form) use ($id) {
        if (null === $id) {
            ProductCard::getInst()->create($fData);
            $form->addNotification("La carte de produits a bien été ajoutée", "success");
        } else {
            ProductCard::getInst()->update($fData, [
                "id" => $fData['id'],
            ]);
            $form->addNotification("La carte de produits a bien été mise à jour", "success");
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




