<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Object\ShopHasProductLang;
use Module\Ekom\Api\Object\UserHasAddress;
use Module\Ekom\Back\User\EkomNullosUser;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
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

$langs = LangLayer::getLangItems();

$langId = (array_key_exists("lang_id", $_GET)) ? (int)$_GET['lang_id'] : EkomNullosUser::getEkomValue("lang_id");
$isReadOnly = (array_key_exists("lang_id", $_GET));


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Translation for product #$avatar",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-shop_has_product_lang")
        ->addControl(SokoInputControl::create()
            ->setName("product_id")
            ->setLabel('Product id')
            ->setProperties([
//                'disabled' => true,
                'readonly' => true,
            ])
            ->setValue($id)
        )
        ->addControl(SokoChoiceControl::create()
            ->setName("lang_id")
            ->setLabel('Lang id')
            ->setChoices($langs)
            ->setValue($langId)
            ->setProperties([
//                'disabled' => true,
                'readonly' => $isReadOnly,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("label")
            ->setLabel('Label')
        )
        ->addControl(SokoInputControl::create()
            ->setName("description")
            ->setLabel('Description')
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("slug")
            ->setLabel('Slug')
        )
        ->addControl(SokoInputControl::create()
            ->setName("out_of_stock_text")
            ->setLabel('Out of stock text')
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_title")
            ->setLabel('Meta title')
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_description")
            ->setLabel('Meta description')
            ->setType("textarea")
        )
        ->addControl(SokoInputControl::create()
            ->setName("meta_keywords")
            ->setLabel('Meta keywords')
            ->setType("textarea")
        )
        ->addValidationRule("slug", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product_lang"),
    'process' => function ($fData, SokoFormInterface $form) use ($shopId, $id, $langId) {
        $fData['shop_id'] = $shopId;
        $fData['product_id'] = $id;

        if (0 === $langId) {
            ShopHasProductLang::getInst()->create($fData);
            $form->addNotification("La traduction pour ce produit a bien été ajoutée", "success");
        } else {
            ShopHasProductLang::getInst()->update($fData, [
                "shop_id" => $shopId,
                "product_id" => $id,
                "lang_id" => $fData['lang_id'],
            ]);
            $form->addNotification("La traduction pour ce produit a bien été mise à jour", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'product_id',
        'lang_id',
    ],
];




