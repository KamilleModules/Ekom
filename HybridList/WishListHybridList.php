<?php


namespace Module\Ekom\HybridList;


use HybridList\HybridList;
use Module\Ekom\Api\Layer\ProductBoxLayer;

class WishListHybridList extends HybridList
{

    protected function preparePhpItems(array $items)
    {
        $ret = [];
        foreach ($items as $row) {
            $productId = $row["product_id"];
            $productDetails = $row["product_details"];
            if ("" !== trim($productDetails)) {
                $productDetails = unserialize($productDetails);
            } else {
                $productDetails = [];
            }
            $box = ProductBoxLayer::getProductBoxByProductId($productId, $productDetails);
            $box['date'] = $row['date'];
            $ret[$productId] = $box;
        }
        return $ret;
    }

}