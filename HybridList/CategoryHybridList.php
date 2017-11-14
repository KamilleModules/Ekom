<?php


namespace Module\Ekom\HybridList;



use HybridList\HybridList;
use Module\Ekom\Api\Layer\ProductBoxLayer;

class CategoryHybridList extends HybridList{

    protected function preparePhpItems(array $items)
    {
        $ret = [];
        foreach($items as $cardId){
            $ret[$cardId] = ProductBoxLayer::getProductBoxByCardId($cardId);
        }
        return $ret;
    }

}