<?php

namespace Controller\Ekom\Back\Catalog;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;

class ShopHasProductProviderListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        $id = $this->getContextFromUrl('id');
        $avatar = ProductLayer::getReferenceByProductId($id);
        return $this->doRenderFormList([
            'title' => "Providers for product #$avatar",
            'menuCurrentRoute' => "NullosAdmin_Ekom_ShopHasProduct_List",
            'breadcrumb' => "shop_has_product_has_provider",
            'form' => "shop_has_product_has_provider",
            'list' => "shop_has_product_has_provider",
            'ric' => [
//                "shop_id",
                "product_id",
                "provider_id",
            ],
            'newItemBtnText' => "Add a new provider for product #$avatar",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_ShopHasProductProvider_List") . "?form&id=" . $id,
            "buttons" => [
                [
                    "label" => "Back to product #$avatar page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_ShopHasProduct_List") . "?product_id=" . $id,
                ],
            ],
            'context' => [
                "id" => $id,
                "avatar" => $avatar,
            ],
        ]);
    }




}