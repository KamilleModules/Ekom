<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Object\ProductGroup;
use Module\Ekom\Api\Object\Tag;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");
$id = (array_key_exists('id', $_GET)) ? (int)$_GET['id'] : 0;


$form = SokoForm::create()
    ->setName("soko-form-product_group")
    ->addControl(SokoInputControl::create()
        ->setName("id")
        ->setLabel('Id')
        ->setProperties([
            'readonly' => true,
        ])
        ->setValue($id)
    )
    ->addControl(SokoInputControl::create()
        ->setName("name")
        ->setLabel('Name')
    )
    ->addValidationRule("name", SokoNotEmptyValidationRule::create());


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Product group",
    //--------------------------------------------
    // SOKO FORM
    'form' => $form,
    'feed' => MorphicHelper::getFeedFunction("ek_product_group"),
    'process' => function ($fData, SokoFormInterface $form) use ($id, $shopId) {
        $fData['shop_id'] = $shopId;
        if (0 === $id) {
            ProductGroup::getInst()->create($fData);
            $form->addNotification("Le groupe de produit a bien été ajouté", "success");
        } else {
            ProductGroup::getInst()->update($fData, [
                "id" => $id,
            ]);
            $form->addNotification("Le groupe de produit a bien été mis à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'id',
    ],
    'formAfterElements' => [
        [
            "type" => "pivotLinks",
            "links" => [
                [
                    "link" => E::link("NullosAdmin_Ekom_ProductGroupHasProduct_List") . "?id=$id",
                    "text" => "Voir les produits liés à ce groupe",
                ],
            ],
        ],
    ],
];