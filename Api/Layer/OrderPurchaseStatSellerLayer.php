<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\OrderPurchaseStatCoupon;
use Module\Ekom\Api\Object\OrderPurchaseStatProvider;
use Module\Ekom\Api\Object\OrderPurchaseStatSeller;
use Module\Ekom\Api\Object\ProductPurchaseStatCategory;
use Module\Ekom\Helper\SqlQueryHelper;
use Module\ThisApp\Ekom\Helper\CartHelper;
use QuickPdo\Helper\QuickPdoHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class OrderPurchaseStatSellerLayer
{


    /**
     * @param $orderId
     * @param array $orderModel
     * @see EkomModels::orderModel()
     */
    public static function insertStatsByOrderModel($orderId, array $orderModel)
    {
        $items = $orderModel['order_details']['cartModel']['itemsGroupedBySeller'];
        foreach ($items as $sellerName => $sellerCart) {


            $totalQuantity = 0;
            $items = $sellerCart['items'];
            foreach ($items as $item) {
                $totalQuantity += $item['cart_quantity'];
            }


            OrderPurchaseStatSeller::getInst()->create([
                "order_id" => $orderId,
                "seller_label" => $sellerCart['label'],
                "amount" => $sellerCart["total"],
                "quantity" => $totalQuantity,
                "date_purchase" => date('Y-m-d H:i:s'),
            ]);
        }
    }
}