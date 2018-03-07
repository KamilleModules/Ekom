<?php

namespace Controller\Ekom\Back\Product;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;

class ProductHasFeatureListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $id = $this->getContextFromUrl('id');
        $avatar = ProductLayer::getReferenceByProductId($id);

        return $this->doRenderFormList([
            'title' => "Features bound to product \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_ProductHasFeature_List",
            'breadcrumb' => "product_has_feature",
            'form' => "product_has_feature",
            'list' => "product_has_feature",
            'ric' => [
                "product_id",
                "feature_id",
            ],
            'newItemBtnText' => "Add a new feature combination for product \"$avatar\"",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_ProductHasFeature_List") . "?form&id=" . $id,
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