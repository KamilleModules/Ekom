<?php


namespace Module\Ekom\Status;


/**
 * @link https://github.com/KamilleModules/Ekom/tree/master/doc/ekom-statuses/ekom-statuses.md
 */
class EkomOrderStatus
{

    const STATUS_PAYMENT_SENT = 'payment_sent';
    const STATUS_PAYMENT_ACCEPTED = 'payment_accepted';
    const STATUS_PAYMENT_VERIFIED = 'payment_verified';
    const STATUS_PREPARING_ORDER = 'preparing_order';
    const STATUS_ORDER_SHIPPED = 'order_shipped';
    const STATUS_ORDER_DELIVERED = 'order_delivered';

    const STATUS_PAYMENT_ERROR = 'payment_error';
    const STATUS_PREPARING_ORDER_ERROR = 'preparing_order_error';
    const STATUS_SHIPPING_ERROR = 'shipping_error';
    const STATUS_ORDER_DELIVERED_ERROR = 'order_delivered_error';

    const STATUS_CANCELED = 'canceled';
    const STATUS_REIMBURSED = 'reimbursed';
}