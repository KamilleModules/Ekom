<?php


namespace Module\Ekom\Model\Orders;


use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\OrderStatusLayer;

class OrderStatusModel
{


    public static function getModelByOrderId($id)
    {

        $key = "id-form-order-status-update";
        $submitStatus = $_POST[$key] ?? null;

        if ($submitStatus && array_key_exists('status', $_POST)) {
            $status = $_POST['status'];
            OrderLayer::addOrderStatusById($id, $status, ['extra' => 'manual']);
        }


        $statuses = OrderStatusLayer::getOrderStatusInfoByOrderId($id);
        $statusList = OrderStatusLayer::getOrderStatusListItems();
        return [
            "statuses" => $statuses,
            "statusList" => $statusList,
            "formKey" => $key,
        ];
    }
}