<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\OrderPurchaseStatCoupon;
use Module\Ekom\Api\Object\ProductPurchaseStatCategory;
use Module\Ekom\Helper\SqlQueryHelper;
use Module\ThisApp\Ekom\Helper\CartHelper;
use QuickPdo\Helper\QuickPdoHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class OrderPurchaseStatCouponLayer
{


    /**
     * @param $orderId
     * @param array $orderModel
     * @see EkomModels::orderModel()
     */
    public static function insertStatsByOrderModel($orderId, array $orderModel)
    {
        $couponsDetails = $orderModel['order_details']['cartModel']['cart']['coupons_details'];
        foreach ($couponsDetails as $couponsDetail) {

            $code = $couponsDetail['code'];
            $couponInfo = CouponLayer::getCouponInfoByCode($code);
            $sellerLabel = (string)$couponInfo['seller_label'];
            $actionType = $couponInfo['action_type'];
            $actionValue = (string)$couponInfo['action_value'];


            OrderPurchaseStatCoupon::getInst()->create([
                "order_id" => $orderId,
                "label" => $couponsDetail['label'],
                "code" => $code,
                "seller_label" => $sellerLabel,
                "action_type" => $actionType,
                "action_value" => $actionValue,
                "saving" => $couponsDetail['amount'],
                "date_used" => date('Y-m-d H:i:s'),
                "user_email" => $orderModel['user_info']['email'],
            ]);
        }
    }
}