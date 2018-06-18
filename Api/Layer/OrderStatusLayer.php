<?php


namespace Module\Ekom\Api\Layer;

use Module\Ekom\Status\EkomOrderStatus;
use QuickPdo\QuickPdo;


/**
 * This class might totally override the StatusLayer class...
 */
class OrderStatusLayer
{

    public static function getCodeById(int $id)
    {
        return QuickPdo::fetch("select code from ek_order_status where id=$id", [], \PDO::FETCH_COLUMN);
    }

    public static function getOrderStatusInfos()
    {
        return QuickPdo::fetchAll("select * from ek_order_status");
    }

    public static function getOrderStatusLabelByCode(string $code)
    {
        return QuickPdo::fetch("select label from ek_order_status where code=:code", ["code" => $code], \PDO::FETCH_COLUMN);
    }

    public static function getOrderStatusInfoByOrderId($id)
    {
        $id = (int)$id;
        return QuickPdo::fetchAll("
select 
h.*,        
s.*

from ek_order_has_order_status h 
inner join ek_order_status s on s.id=h.order_status_id

where h.order_id=$id
order by h.date desc
        
        ");
    }

    /**
     * @return array of statuses which indicate that the money has NOT been taken yet from the user.
     */
    public static function getPaymentNotOkStatuses()
    {
        $statuses = [
            EkomOrderStatus::STATUS_PAYMENT_SENT,
            EkomOrderStatus::STATUS_PAYMENT_ERROR,
            EkomOrderStatus::STATUS_REIMBURSED,
            EkomOrderStatus::STATUS_CANCELED,
            EkomOrderStatus::STATUS_ORDER_DELIVERED_ERROR,
            EkomOrderStatus::STATUS_SHIPPING_ERROR,
            EkomOrderStatus::STATUS_PREPARING_ORDER_ERROR,
        ];
        $sStatuses = "'" . implode("', '", $statuses) . "'";
        return QuickPdo::fetchAll("
select id from ek_order_status where `code` in ($sStatuses)        
        ", [], \PDO::FETCH_COLUMN);

    }

    public static function getSuccessfulStatusIds()
    {
        $statuses = [
            EkomOrderStatus::STATUS_PAYMENT_ACCEPTED,
            EkomOrderStatus::STATUS_PAYMENT_VERIFIED,
        ];
        $sStatuses = "'" . implode("', '", $statuses) . "'";
        return QuickPdo::fetchAll("
select id from ek_order_status where `code` in ($sStatuses)        
        ", [], \PDO::FETCH_COLUMN);

    }

    public static function getOrderStatusListItems()
    {
        return QuickPdo::fetchAll("select id, label from ek_order_status", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getOrderStatusCode2Label()
    {
        return QuickPdo::fetchAll("select code, label from ek_order_status", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getOrderStatusLabel2BgColor()
    {
        return QuickPdo::fetchAll("select label, bg_color from ek_order_status", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

}
