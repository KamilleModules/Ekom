<?php

namespace Controller\Ekom\Back\ProductCard;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\ProductCardLayer;
use Module\Ekom\Utils\E;

class ProductCardLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $id = $this->getContextFromUrl('id');
        $avatar = ProductCardLayer::getRepresentationById($id);

        return $this->doRenderFormList([
            'title' => "Translations for product card \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_ProductCard_List",
            'breadcrumb' => "product_card_lang",
            'form' => "product_card_lang",
            'list' => "product_card_lang",
            'ric' => [
                "product_card_id",
                "lang_id",
            ],
            'newItemBtnText' => "Add a new translation for product card $avatar",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_ProductCardLang_List") . "?form&id=" . $id,
            "buttons" => [
                [
                    "label" => "Back to product \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_ProductCard_List") . "?id=" . $id,
                ],
            ],
            'context' => [
                "id" => $id,
            ],
        ]);
    }


}