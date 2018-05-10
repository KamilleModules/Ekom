<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class ProductReferenceLayer
{

    public static function getIdByReference(string $reference)
    {
        return QuickPdo::fetch("select id from ek_product_reference where reference=:ref", [
            "ref" => $reference,
        ], \PDO::FETCH_COLUMN);
    }

    /**
     *
     * This is a backoffice method for forms of product without details.
     *
     *
     * Note that this method is supposed to be called only in an environment
     * where your product doesn't have product details.
     *
     * Why? Because a product id could have many product references if the product
     * has details.
     *
     * If your product has details, and you are calling this method, you should
     * re-consider your call.
     *
     */
    public static function getFirstProductReferenceIdByProductId(int $productId)
    {
        return QuickPdo::fetch("select id from ek_product_reference where product_id=$productId", [], \PDO::FETCH_COLUMN);
    }


    public static function getQuantityByReference(string $reference)
    {
        return QuickPdo::fetch("select quantity from ek_product_reference where reference=:ref", [
            "ref" => $reference,
        ], \PDO::FETCH_COLUMN);
    }

    public static function getReference2QuantityMap(array $references)
    {
        if ($references) {
            $markers = [];
            $sRef = QuickPdoStmtTool::prepareInString($references, $markers);
            return QuickPdo::fetchAll("select reference, quantity from ek_product_reference where reference in ($sRef)", $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }
        return [];
    }

}