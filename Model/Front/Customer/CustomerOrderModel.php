<?php


namespace Module\Ekom\Model\Front\Customer;


use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\HybridList\HybridListFactory;


/**
 * Note: this DynamicProductListModel is based on the constraints in my company:
 * where the price depends on the user (origin country, shipping country, group, ...).
 *
 * Therefore, it's highly dynamical and very expensive in terms of performance.
 * We can't do it without a good caching strategy.
 *
 * Please if you need a less complex model create another model (like SimpleProductListModel for
 * instance) and redo your own logic.
 * Remember that controllers should be thin and used to branch models (like this one)
 * to the view.
 *
 *
 */
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
            $item['status_history'] = OrderLayer::getOrderHistoryById($item['id']);
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