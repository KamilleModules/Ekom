<?php


namespace Module\Ekom\Api\Layer;


use Bat\StringTool;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\OrderPurchaseStatCoupon;
use Module\Ekom\Api\Object\OrderPurchaseStatProvider;
use Module\Ekom\Api\Object\ProductPurchaseStatCategory;
use Module\Ekom\Helper\SqlQueryHelper;
use Module\ThisApp\Ekom\Helper\CartHelper;
use QuickPdo\Helper\QuickPdoHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class OrderPurchaseStatProviderLayer
{


    /**
     * @param $orderId
     * @param array $orderModel
     * @see EkomModels::orderModel()
     */
    public static function insertStatsByOrderModel($orderId, array $orderModel)
    {
        $items = $orderModel['order_details']['cartModel']['cart']['items'];
        $providerInfos = [];


        //--------------------------------------------
        // COLLECT INFOS
        //--------------------------------------------
        foreach ($items as $item) {
            $providerId = $item['provider_id'];
            if ($providerId) {
                if (false === array_key_exists($providerId, $providerInfos)) {
                    $providerLabel = ProviderLayer::getLabelById($providerId);
                    $providerInfo = [
                        "label" => $providerLabel,
                        "quantity" => 0,
                        "amount" => 0,
                    ];
                    $providerInfos[$providerId] = $providerInfo;
                }

                $providerInfos[$providerId]["quantity"] += $item['cart_quantity'];
                /**
                 * Note that for now we don't take into accounts the coupons!
                 */
                $providerInfos[$providerId]["amount"] += $item['line_sale_price'];

            }
        }


        //--------------------------------------------
        // NOW INSERT STATS
        //--------------------------------------------
        foreach ($providerInfos as $providerInfo) {
            OrderPurchaseStatProvider::getInst()->create([
                "order_id" => $orderId,
                "provider_label" => $providerInfo['label'],
                "amount" => $providerInfo["amount"],
                "quantity" => $providerInfo['quantity'],
                "date_purchase" => date('Y-m-d H:i:s'),
            ]);
        }
    }
}