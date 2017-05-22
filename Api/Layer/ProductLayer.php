<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;

class ProductLayer
{


    /**
     * @return false|int, the id of the product card which slug was given, or false if there is no matching product card.
     */
    public function getProductCardIdBySlug($slug)
    {
        return EkomApi::inst()->productCardLang()->readColumn("product_card_id", [["slug", '=', $slug]]);
    }

}