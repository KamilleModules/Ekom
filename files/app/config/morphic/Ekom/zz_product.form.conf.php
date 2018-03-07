<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Layer\UserGroupLayer;
use Module\Ekom\Api\Object\Product;
use Module\Ekom\Api\Object\ProductCard;
use Module\Ekom\Api\Object\UserHasAddress;
use Module\Ekom\Api\Object\UserHasUserGroup;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\FormHelper;
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


$id = (array_key_exists('id', $_GET)) ? (int)$_GET['id'] : null;


$afterElements = [];
if (null !== $id) {
    $afterElements[] = [
        "type" => "pivotLinks",
        "links" => [
            [
                "text" => "Voir les traductions pour ce produit",
                "link" => E::link("NullosAdmin_Ekom_ProductLang_List") . "?id=$id",
            ],
            [
                "text" => "Voir les attributs assoc!és à ce produit",
                "link" => E::link("NullosAdmin_Ekom_ProductHasAttribute_List") . "?id=$id",
            ],
            [
                "text" => "Voir les caractéristiques assoc!ées à ce produit",
                "link" => E::link("NullosAdmin_Ekom_ProductHasFeature_List") . "?id=$id",
            ],
        ],
    ];
}

$productCardControl = SokoAutocompleteInputControl::create()
    ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
        'action' => "auto.product_card",
    ]))
    ->setName("product_card_id")
    ->setLabel('Product card id');


if (false) {
    $productCards = ProductCardLayer::getItems();
    $productCardControl = SokoChoiceControl::create()
        ->setName("product_card_id")
        ->setLabel('Product card')
        ->setChoices($productCards);
}


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-product")
        ->addControl(SokoInputControl::create()
            ->setName("id")
            ->setLabel('Id')
            ->setProperties([
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl($productCardControl)
        ->addControl(SokoInputControl::create()
            ->setName("reference")
            ->setLabel('Reference')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("weight")
            ->setLabel('Weight')
        )
        ->addControl(SokoInputControl::create()
            ->setName("price")
            ->setLabel('Price')
            ->setProperties([
                'required' => true,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("width")
            ->setLabel('Width')
        )
        ->addControl(SokoInputControl::create()
            ->setName("height")
            ->setLabel('Height')
        )
        ->addControl(SokoInputControl::create()
            ->setName("depth")
            ->setLabel('Depth')
        )
        ->addValidationRule("product_card_id", SokoNotEmptyValidationRule::create())
        ->addValidationRule("reference", SokoNotEmptyValidationRule::create())
        ->addValidationRule("price", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_product"),
    'process' => function ($fData, SokoFormInterface $form) use ($id) {

        $fData['price'] = FormHelper::sanitizePrice($fData['price']);
        if (null === $id) {
            Product::getInst()->create($fData);
            $form->addNotification("Le produit a bien été ajouté", "success");
        } else {
            Product::getInst()->update($fData, [
                "id" => $fData['id'],
            ]);
            $form->addNotification("Le produit a bien été mis à jour", "success");
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




