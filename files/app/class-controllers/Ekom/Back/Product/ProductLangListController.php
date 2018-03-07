<?php

namespace Controller\Ekom\Back\Product;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;

class ProductLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $id = $this->getContextFromUrl('id');
        $avatar = ProductLayer::getReferenceByProductId($id);

        return $this->doRenderFormList([
            'title' => "Translations for product \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_Product_List",
            'breadcrumb' => "product_lang",
            'form' => "product_lang",
            'list' => "product_lang",
            'ric' => [
                "product_id",
                "lang_id",
            ],
            'newItemBtnText' => "Add a new translation for product \"$avatar\"",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_ProductLang_List") . "?form&id=" . $id,
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