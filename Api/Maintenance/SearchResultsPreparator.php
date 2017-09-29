<?php


namespace Module\Ekom\Api\Maintenance;


use QuickPdo\QuickPdo;

class SearchResultsPreparator
{

    public static function create()
    {
        return new static();
    }


    public function fillShopHasProductLangLabels()
    {
        $ret = [];
        $rows = QuickPdo::fetchAll("select shop_id, product_id, lang_id from ek_shop_has_product_lang where label=''");
        foreach ($rows as $row) {
            $shopId = $row['shop_id'];
            $productId = $row['product_id'];
            $langId = $row['lang_id'];

            $label = QuickPdo::fetch("select label from ek_product_lang where product_id=$productId and lang_id=$langId", [], \PDO::FETCH_COLUMN);
            QuickPdo::update("ek_shop_has_product_lang", [
                'label' => $label,
            ], [
                ['shop_id', '=', $shopId],
                ['product_id', '=', $productId],
                ['lang_id', '=', $langId],
            ]);
            $ret[$productId] = $label;
        }
        return $ret;
    }

    public function fillShopHasProductCardLangLabels()
    {
        $ret = [];
        $rows = QuickPdo::fetchAll("select shop_id, product_card_id, lang_id from ek_shop_has_product_card_lang where label=''");
        foreach ($rows as $row) {
            $shopId = $row['shop_id'];
            $cardId = $row['product_card_id'];
            $langId = $row['lang_id'];

            $label = QuickPdo::fetch("select label from ek_product_card_lang where product_card_id=$cardId and lang_id=$langId", [], \PDO::FETCH_COLUMN);
            QuickPdo::update("ek_shop_has_product_card_lang", [
                'label' => $label,
            ], [
                ['shop_id', '=', $shopId],
                ['product_card_id', '=', $cardId],
                ['lang_id', '=', $langId],
            ]);
            $ret[$cardId] = $label;
        }
        return $ret;
    }


}