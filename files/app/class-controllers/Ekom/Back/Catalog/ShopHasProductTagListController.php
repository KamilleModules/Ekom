<?php

namespace Controller\Ekom\Back\Catalog;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;

class ShopHasProductTagListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        $id = $this->getContextFromUrl('id');
        $avatar = ProductLayer::getReferenceByProductId($id);
        return $this->doRenderFormList([
            'title' => "Tags for product #$avatar",
            'menuCurrentRoute' => "NullosAdmin_Ekom_ShopHasProduct_List",
            'breadcrumb' => "shop_has_product_has_tag",
            'form' => "shop_has_product_has_tag",
            'list' => "shop_has_product_has_tag",
            'ric' => [
//                "shop_id",
                "product_id",
                "tag_id",
            ],
            'newItemBtnText' => "Add a new tag for product #$avatar",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_ShopHasProductTag_List") . "?form&id=" . $id,
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