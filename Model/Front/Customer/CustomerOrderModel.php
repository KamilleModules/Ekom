<?php


namespace Module\Ekom\Model\Front\Customer;


use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\HybridList\HybridListFactory;



class CustomerOrderModel
{

    public static function getModel(array $pool, $userId)
    {


        if (false === array_key_exists("sort", $pool)) {
            $pool['sort'] = "date_desc";
        }
        $hybridList = HybridListFactory::getOrderHybridList($pool, $userId);
        $info = $hybridList->execute();


        $items = $info['items'];


        OrderLayer::unserializeRows($items);
        foreach ($items as $k => $item) {
            $item['status_history'] = OrderLayer::getOrderHistoryById($item['id'], [
                "skipIdenticalSiblings" => true,
            ]);
            $items[$k] = $item;
        }
        $info['items'] = $items;


        $model['bundle'] = [
            'general' => $info,
            'slice' => $hybridList->getControl('slice')->getModel(),
            'sort' => $hybridList->getControl('sort')->getModel(),
        ];
        return $model;
    }
}