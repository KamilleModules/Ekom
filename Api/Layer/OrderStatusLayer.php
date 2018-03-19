<?php


namespace Module\Ekom\Api\Layer;

use Module\Ekom\Status\EkomOrderStatus;
use QuickPdo\QuickPdo;


/**
 * This class might totally override the StatusLayer class...
 */
class OrderStatusLayer
{
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

}
