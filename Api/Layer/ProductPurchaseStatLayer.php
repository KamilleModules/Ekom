<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\ProductPurchaseStatCategory;
use Module\Ekom\Helper\SqlQueryHelper;
use Module\ThisApp\Ekom\Helper\CartHelper;
use QuickPdo\Helper\QuickPdoHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class ProductPurchaseStatLayer
{

    public static function insertStatsByCart($orderId, array $extendedCartModel, $userId)
    {
        $cart = $extendedCartModel['cart'];
        foreach ($cart['items'] as $box) {


            $id = EkomApi::inst()->productPurchaseStat()->create([
                "purchase_date" => date("Y-m-d H:i:s"),
                "order_id" => $orderId,
                "user_id" => $userId,
                "product_id" => $box['product_id'],
                "product_ref" => $box['reference'],
                "product_label" => $box['label'],
                "quantity" => $box['cart_quantity'],
                "price" => $box['sale_price'],
                "price_without_tax" => $box['base_price'],
                "total" => $box['line_sale_price'],
                "total_without_tax" => $box['line_base_price'],
                "attribute_selection" => serialize($box['selected_attributes_info']),
                "product_details_selection" => serialize($box['selected_product_details_info']),
                "wholesale_price" => $box['wholesale_price'],
            ]);


            $productCardId = $box['product_card_id'];
            $categoryIds = CategoryLayer::getCategoryIdsByProductCardId($productCardId);
            foreach ($categoryIds as $categoryId) {
                ProductPurchaseStatCategory::getInst()->create([
                    "product_purchase_stat_id" => $id,
                    "category_id" => $categoryId,
                ]);
            }
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