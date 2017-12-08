<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class OrderLayer
{

    public static function getRefById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("
select reference from ek_order where id=$id        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getOrderInfo($id)
    {
        $id = (int)$id;
        return A::cache()->get("Ekom.OrderLayer.getOrderInfo.$id.", function () use ($id) {

            $row = QuickPdo::fetch("
select * from ek_order where id=$id 
        ");
            self::unserializeRow($row);
            return $row;
        });
    }

    public static function getOrderHistoryById($orderId, $langId = null)
    {
        $langId = E::getLangId($langId);
        $orderId = (int)$orderId;
        return QuickPdo::fetchAll("
select 
s.code,
s.color,
l.label

from ek_order_status s 
inner join ek_order_has_order_status h on h.order_status_id=s.id
inner join ek_order_status_lang l on l.order_status_id=s.id
 

where h.order_id=$orderId
and l.lang_id=$langId
order by date asc         
        ");
    }


    public static function addOrderStatusByCode($orderId, $code, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $code2Ids = self::getCode2Ids($shopId);
        if (array_key_exists($code, $code2Ids)) {
            $orderStatusId = $code2Ids[$code];
            return EkomApi::inst()->orderHasOrderStatus()->create([
                "order_id" => $orderId,
                "order_status_id" => $orderStatusId,
                "date" => date("Y-m-d H:i:s"),
            ]);
        }
        throw new EkomException("Unknown code: $code");
    }

    public static function getCode2Ids($shopId)
    {
        return A::cache()->get("Ekom.OrderLayer.$shopId", function () use ($shopId) {
            return EkomApi::inst()->orderStatus()->readKeyValues("code", "id", [
                "where" => [
                    ["shop_id", "=", $shopId],
                ],
            ]);
        });
    }

    public static function countOrders()
    {
        return QuickPdo::fetch("select count(*) as count from ek_order", [], \PDO::FETCH_COLUMN);
    }

    public static function countUserOrders($userId)
    {
        $userId = (int)$userId;
        return QuickPdo::fetch("
select count(*) as count from ek_order 
where user_id=$userId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getLastOrderInfoByUserId($userId)
    {
        $userId = (int)$userId;
        if (false !== ($row = QuickPdo::fetch("
select * from ek_order where user_id=$userId
order by `date` desc        
        "))) {
            self::unserializeRow($row);
            return $row;
        }
        return false;
    }


    public static function getNbUserPendingOrders($userId)
    {
        $pendingForbidden = implode(', ', StatusLayer::getPendingForbidden("'"));
        $userId = (int)$userId;
        return QuickPdo::fetch("
select

count(*) as count

from ek_order o

where
o.user_id=$userId
and 
(
  select
  s.code
  from ek_order_status s
  inner join ek_order_has_order_status h on h.order_status_id=s.id
  where order_id=o.id
  order by h.date DESC
  limit 0,1

) not in ($pendingForbidden)


", [], \PDO::FETCH_COLUMN);


    }

    public static function unserializeRows(array &$rows)
    {
        foreach ($rows as $k => $row) {
            self::unserializeRow($row);
            $rows[$k] = $row;
        }
    }

    public static function unserializeRow(array &$row)
    {
        $row['user_info'] = unserialize($row['user_info']);
        $row['shop_info'] = unserialize($row['shop_info']);
        $row['shipping_address'] = unserialize($row['shipping_address']);
        $row['billing_address'] = unserialize($row['billing_address']);
        $row['order_details'] = unserialize($row['order_details']);
    }
}
