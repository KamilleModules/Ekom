<?php


namespace Module\Ekom\HybridList;



use HybridList\HybridList;
use Module\Ekom\Api\Layer\ProductBoxLayer;

class CategoryHybridList extends HybridList{

    protected function preparePhpItems(array $items)
    {
        $ret = [];
        foreach($items as $row){
            $cardId = $row["product_card_id"];
            $productId = $row["product_id"];
            $ret[$cardId] = ProductBoxLayer::getProductBoxByCardId($cardId, $productId);
        }
        return $ret;
    }

}