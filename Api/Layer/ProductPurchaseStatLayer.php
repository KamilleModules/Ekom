<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use Module\Ekom\Api\EkomApi;
use Module\ThisApp\Ekom\Helper\CartHelper;
use QuickPdo\QuickPdo;

class ProductPurchaseStatLayer
{

    public static function insertStatsByCart(array $cart, $shopId, $userId, $currencyId)
    {
        foreach ($cart['items'] as $box) {

            $box['productDetailsSelection'];

            EkomApi::inst()->productPurchaseStat()->create([
                "purchase_date" => date("Y-m-d H:i:s"),
                "shop_id" => $shopId,
                "user_id" => $userId,
                "currency_id" => $currencyId,
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
}