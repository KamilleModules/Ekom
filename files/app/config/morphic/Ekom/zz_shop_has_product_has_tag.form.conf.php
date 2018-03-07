<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\TagLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Object\ShopHasProductHasTag;
use Module\Ekom\Api\Object\ShopHasProductLang;
use Module\Ekom\Api\Object\UserHasAddress;
use Module\Ekom\Back\Helper\BackFormHelper;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Control\SokoAutocompleteInputControl;
use SokoForm\Control\SokoBooleanChoiceControl;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoForm;
use SokoForm\Form\SokoFormInterface;
use SokoForm\ValidationRule\SokoNotEmptyValidationRule;


//--------------------------------------------
// FORM WITH CONTEXT
//--------------------------------------------
$id = MorphicHelper::getFormContextValue("id", $context); // userId
$avatar = MorphicHelper::getFormContextValue("avatar", $context);
$shopId = EkomNullosUser::getEkomValue("shop_id");


$tagId = (array_key_exists("tag_id", $_GET)) ? (int)$_GET['tag_id'] : 0;
$isReadOnly = (0 !== $tagId);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Tags for product #$avatar",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-shop_has_product_has_tag")
        ->addControl(SokoInputControl::create()
            ->setName("product_id")
            ->setLabel('Product id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoAutocompleteInputControl::create()
            ->setAutocompleteOptions(BackFormHelper::createSokoAutocompleteOptions([
                'action' => "auto.tag",
            ]))
            ->setName("tag_id")
            ->setLabel('Tag id')
            ->setValue($tagId)
            ->setProperties([
//                'disabled' => true,
                'readonly' => $isReadOnly,
            ])
        )
        ->addValidationRule("tag_id", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product_has_tag"),
    'process' => function ($fData, SokoFormInterface $form) use ($shopId, $id, $tagId) {
        $fData['shop_id'] = $shopId;
        $fData['product_id'] = $id;

        if (0 === (int)$tagId) {
            ShopHasProductHasTag::getInst()->create($fData);
            $form->addNotification("La traduction pour ce tag a bien été ajoutée", "success");
        } else {
            ShopHasProductHasTag::getInst()->update($fData, [
                "shop_id" => $shopId,
                "product_id" => $id,
                "tag_id" => $fData['tag_id'],
            ]);
            $form->addNotification("La traduction pour ce tag a bien été mise à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'product_id',
        'tag_id',
    ],
];




