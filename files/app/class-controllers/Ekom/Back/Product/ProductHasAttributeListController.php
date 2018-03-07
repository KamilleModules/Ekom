<?php

namespace Controller\Ekom\Back\Product;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;

class ProductHasAttributeListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $id = $this->getContextFromUrl('id');
        $avatar = ProductLayer::getReferenceByProductId($id);

        return $this->doRenderFormList([
            'title' => "Attributes bound to product \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_ProductHasAttribute_List",
            'breadcrumb' => "product_has_product_attribute",
            'form' => "product_has_product_attribute",
            'list' => "product_has_product_attribute",
            'ric' => [
                "product_id",
                "product_attribute_id",
                "product_attribute_value_id",
            ],
            'newItemBtnText' => "Add a new attribute combination for product \"$avatar\"",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_ProductHasAttribute_List") . "?form&id=" . $id,
            "buttons" => [
                [
                    "label" => "Back to product \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Product_List") . "?id=" . $id,
                ],
            ],
            'context' => [
                "id" => $id,
            ],
        ]);
    }


}