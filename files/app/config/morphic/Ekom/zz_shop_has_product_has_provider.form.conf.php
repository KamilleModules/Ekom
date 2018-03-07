<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Api\Layer\AddressLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ProviderLayer;
use Module\Ekom\Api\Layer\TagLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Object\ShopHasProductHasProvider;
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
$providers = ProviderLayer::getItems($shopId);

$providerId = (array_key_exists("provider_id", $_GET)) ? (int)$_GET['provider_id'] : 0;
$isReadOnly = (0 !== $providerId);


$conf = [
    //--------------------------------------------
    // FORM WIDGET
    //--------------------------------------------
    'title' => "Providers for product #$avatar",
    //--------------------------------------------
    // SOKO FORM
    'form' => SokoForm::create()
        ->setName("soko-form-shop_has_product_has_provider")
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
            ->setName("provider_id")
            ->setLabel('Provider id')
            ->setChoices($providers)
            ->setValue($providerId)
            ->setProperties([
//                'disabled' => true,
                'readonly' => $isReadOnly,
            ])
        )
        ->addControl(SokoInputControl::create()
            ->setName("wholesale_price")
            ->setLabel('Wholesale price')
        )
        ->addValidationRule("provider_id", SokoNotEmptyValidationRule::create())
    ,
    'feed' => MorphicHelper::getFeedFunction("ek_shop_has_product_has_provider"),
    'process' => function ($fData, SokoFormInterface $form) use ($shopId, $id, $providerId) {
        $fData['shop_id'] = $shopId;
        $fData['product_id'] = $id;
        $fData['wholesale_price'] = MorphicHelper::price($fData['wholesale_price']);

        if (0 === (int)$providerId) {
            ShopHasProductHasProvider::getInst()->create($fData);
            $form->addNotification("Le fournisseur a bien été ajouté pour ce produit", "success");
        } else {
            ShopHasProductHasProvider::getInst()->update($fData, [
                "shop_id" => $shopId,
                "product_id" => $id,
                "provider_id" => $fData['provider_id'],
            ]);
            $form->addNotification("Le fournisseur a bien été mis à jour pour ce produit", "success");
        }
        return false;
    },
    //--------------------------------------------
    // to fetch values
    'ric' => [
        'product_id',
        'provider_id',
    ],
];




