<?php


namespace Module\Ekom\Model\Front\Customer;


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


        $hybridList = HybridListFactory::getOrderHybridList($pool, $userId);
        $info = $hybridList->execute();

        $model['bundle'] = [
            'general' => $info,
            'slice' => $hybridList->getControl('slice')->getModel(),
            'sort' => $hybridList->getControl('sort')->getModel(),
        ];
        return $model;
    }
}