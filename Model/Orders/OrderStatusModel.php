<?php


namespace Module\Ekom\Model\Orders;


use Bat\HashTool;
use Bat\RandomTool;
use Bat\StringTool;
use Module\Ekom\Api\Layer\OrderStatusLayer;

class OrderStatusModel
{


    public static function getModelByOrderId($id)
    {

        $key = "id-form-order-status-update";
        $submitStatus = $_POST[$key] ?? null;

a($_POST);
        if ($submitStatus) {
            az(__FILE__, "here");
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