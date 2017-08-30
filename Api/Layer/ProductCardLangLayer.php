<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class ProductCardLangLayer
{
    public function hasSlug($slug)
    {
        return (false !== QuickPdo::fetch("select product_card_id from ek_product_card_lang where slug=:slug", [
                'slug' => $slug,
            ]));
    }

    public function getInfoBySlug($slug)
    {
        return QuickPdo::fetch("select * from ek_product_card_lang where slug=:slug", [
            'slug' => $slug,
        ]);
    }


}