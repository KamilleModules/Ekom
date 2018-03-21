<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Helper\SqlQueryHelper;
use Module\ThisApp\Ekom\Helper\CartHelper;
use QuickPdo\Helper\QuickPdoHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class ProductPurchaseStatLayer
{

    public static function insertStatsByCart(array $cart, $userId)
    {
        foreach ($cart['items'] as $box) {

            $box['productDetailsSelection'];

            EkomApi::inst()->productPurchaseStat()->create([
                "purchase_date" => date("Y-m-d H:i:s"),
                "user_id" => $userId,
                "product_id" => $box['product_id'],
                "product_ref" => $box['ref'],
                "product_label" => $box['label'],
                "quantity" => $box['quantityCart'],
                "price" => $box['priceSaleRaw'],
                "price_without_tax" => $box['priceBaseRaw'],
                "total" => $box['priceLineRaw'],
                "total_without_tax" => $box['priceLineWithoutTaxRaw'],
                "attribute_selection" => serialize($box['attributesSelection']),
                "product_details_selection" => serialize($box['productDetailsSelection']),
                "wholesale_price" => $box['wholesale_price'],
            ]);

        }
    }


    public static function getItemsByUserId($userId, $addBox = false)
    {
        $userId = (int)$userId;
        $rows = QuickPdo::fetchAll("
select * from ek_product_purchase_stat 
where user_id=$userId        
        ");

        if (true === $addBox) {
            foreach ($rows as $k => $row) {
                $productDetailsMap = CartHelper::getProductDetailsMapByProductDetailsSelection(StringTool::unserializeAsArray($row['product_details_selection']));
                $box = ProductBoxLayer::getProductBoxByProductId($row['product_id'], $productDetailsMap);
                $row['box'] = $box;
                $rows[$k] = $row;
            }
        }
        return $rows;
    }


    public static function getDate2WholeSalePrice($dateStart = null, $dateEnd = null)
    {
        $q = "select date(purchase_date) as date, sum(wholesale_price) as wholesale_price_sum from ek_product_purchase_stat where 1";
        $markers = [];

        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "purchase_date");
        $q .= " group by date(purchase_date)";
        $all = QuickPdo::fetchAll($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        return $all;
    }
}