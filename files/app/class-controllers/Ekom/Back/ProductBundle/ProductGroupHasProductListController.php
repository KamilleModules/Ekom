<?php

namespace Controller\Ekom\Back\ProductGroup;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Layer\ProductGroupLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;

class ProductGroupHasProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $id = $this->getContextFromUrl('id');
        $avatar = ProductGroupLayer::getReferenceByGroupId($id);

        return $this->doRenderFormList([
            'title' => "Products for product group \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_ProductGroup_List",
            'breadcrumb' => "product_group_has_product",
            'form' => "product_group_has_product",
            'list' => "product_group_has_product",
            'ric' => [
                "product_group_id",
                "product_id",
            ],
            'newItemBtnText' => "Add a new product for product group \"$avatar\"",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_ProductGroupHasProduct_List") . "?form&id=" . $id,
            "buttons" => [
                [
                    "label" => "Back to product group \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_ProductGroup_List") . "?id=" . $id,
                ],
            ],
            'context' => [
                "id" => $id,
            ],
        ]);
    }


}