<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use QuickPdo\QuickPdo;

class OrderLayer
{


    public function getUniqueReference()
    {
        return date('Ymd-His') . '-' . sprintf('%04s', ($this->countOrders() + 1));
    }


    public function addOrderStatusByEkomAction($orderId, $ekomAction)
    {

        if (false !== ($code = X::get("Ekom_StatusProvider")->getCode($ekomAction))) {


            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            $code2Ids = $this->getCode2Ids($shopId);
            if (array_key_exists($code, $code2Ids)) {
                $orderStatusId = $code2Ids[$code];
                return EkomApi::inst()->orderHasOrderStatus()->create([
                    "order_id" => $orderId,
                    "order_status_id" => $orderStatusId,
                    "date" => date("Y-m-d H:i:s"),
                ]);
            }
            XLog::error("[Ekom module] - OrderLayer: code not found ($code) for shop $shopId");
        }
        return false;
    }


    public function getOrderInfo($id)
    {
        $id = (int)$id;
        $userId = EkomApi::inst()->userLayer()->getUserId();

        return A::cache()->get("Ekom.OrderLayer.getOrderInfo.$id", function () use ($id, $userId) {

            $row = QuickPdo::fetch("
select * from ek_order where id=$id and user_id=$userId        
        ");
            $row['user_info'] = unserialize($row['user_info']);
            $row['shop_info'] = unserialize($row['shop_info']);
            $row['shipping_address'] = unserialize($row['shipping_address']);
            $row['billing_address'] = unserialize($row['billing_address']);
            $row['order_details'] = unserialize($row['order_details']);
            return $row;
        }, [
            "ek_order.delete.$id",
            "ek_order.update.$id",
        ]);
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private function countOrders()
    {
        $ret = 0;
        if (false !== ($row = QuickPdo::fetch("
select count(*) as count from ek_order 
where `date` = CURDATE()        
        "))
        ) {
            $ret = (int)$row['count'];
        }
        return $ret;
    }

    public function getCode2Ids($shopId)
    {
        return A::cache()->get("Ekom.OrderLayer.$shopId", function () use ($shopId) {
            return EkomApi::inst()->orderStatus()->readKeyValues("code", "id", [
                "where" => [
                    ["shop_id", "=", $shopId],
                ],
            ]);
        }, [
            "ek_order_status.create",
            "ek_order_status.delete.$shopId",
            "ek_order_status.update.$shopId",
        ]);
    }

}
