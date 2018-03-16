<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\Object\ProductCardImage;

class ProductCardImageLayer
{

    public static function pushImageUrl($cardId, $url)
    {
        $o = new ProductCardImage();
        $o->push(["product_card_id" => $cardId], [
            'url' => $url,
            'product_card_id' => $cardId,
        ]);
    }

}