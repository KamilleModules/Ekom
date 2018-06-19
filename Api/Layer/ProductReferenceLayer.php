<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class ProductReferenceLayer
{


    public static function getIdsByCategoryId(int $categoryId)
    {

        $categoryIds = CategoryLayer::getSelfAndChildrenIdsById($categoryId);
        $sCatIds = '"' . implode('", "', $categoryIds) . '"';
        $ret = QuickPdo::fetchAll("
select pr.id 
from
ek_product_reference pr 
inner join ek_product p on p.id=pr.product_id
inner join ek_category_has_product_card h on h.product_card_id=p.product_card_id  
where h.category_id in ($sCatIds)
", [], \PDO::FETCH_COLUMN);
        $ret = array_unique($ret);
        sort($ret);
        return $ret;
    }


    public static function getIdsByCategoryName(string $categoryName)
    {

        $categoryIds = CategoryLayer::getSelfAndChildrenIdsByName($categoryName);
        $sCatIds = '"' . implode('", "', $categoryIds) . '"';
        $ret = QuickPdo::fetchAll("
select pr.id 
from
ek_product_reference pr 
inner join ek_product p on p.id=pr.product_id
inner join ek_category_has_product_card h on h.product_card_id=p.product_card_id  
where h.category_id in ($sCatIds)
", [], \PDO::FETCH_COLUMN);
        $ret = array_unique($ret);
        sort($ret);
        return $ret;
    }

    public static function getProductReferenceIdsByProductId(int $productId)
    {
        return QuickPdo::fetchAll("select id from ek_product_reference where product_id=$productId", [], \PDO::FETCH_COLUMN);
    }

    public static function getProductIdByReference(string $reference)
    {
        return QuickPdo::fetch("select product_id from ek_product_reference where reference=:ref", [
            "ref" => $reference,
        ], \PDO::FETCH_COLUMN);
    }


    public static function getInfoByReference(string $reference)
    {
        return QuickPdo::fetch("select * from ek_product_reference where reference=:ref", [
            "ref" => $reference,
        ]);
    }

    public static function getInfoAndCardIdByReference(string $reference)
    {
        return QuickPdo::fetch("
select 
pr.*,
p.product_card_id
from ek_product_reference pr 
inner join ek_product p on p.id=pr.product_id 
where reference=:ref", [
            "ref" => $reference,
        ]);
    }

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