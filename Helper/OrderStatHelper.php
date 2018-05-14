<?php


namespace Module\Ekom\Helper;

use Module\Ekom\Api\Layer\OrderPurchaseStatCouponLayer;
use Module\Ekom\Api\Layer\OrderPurchaseStatProviderLayer;
use Module\Ekom\Api\Layer\OrderPurchaseStatSellerLayer;
use Module\Ekom\Api\Layer\ProductPurchaseStatLayer;

class OrderStatHelper
{


    public static function insertOrderStatsByModel(int $orderId, array $orderModel)
    {
        //--------------------------------------------
        // PRODUCT STATS (also handles categories)
        //--------------------------------------------
        ProductPurchaseStatLayer::insertStatsByCart(
            $orderId,
            $orderModel['order_details']['cartModel'],
            $orderModel['user_id']
        );

        //--------------------------------------------
        // COUPONS STATS
        //--------------------------------------------
        OrderPurchaseStatCouponLayer::insertStatsByOrderModel(
            $orderId,
            $orderModel
        );


        //--------------------------------------------
        // PROVIDER STATS
        //--------------------------------------------
        OrderPurchaseStatProviderLayer::insertStatsByOrderModel(
            $orderId,
            $orderModel
        );


        //--------------------------------------------
        // SELLER STATS
        //--------------------------------------------
        OrderPurchaseStatSellerLayer::insertStatsByOrderModel(
            $orderId,
            $orderModel
        );

    }
}