<?php


namespace Module\Ekom\Model\Front\Customer;


use Module\Ekom\HybridList\HybridListFactory;
use Module\Ekom\Utils\E;


class CustomerWishListModel
{

    public static function getModel(array $pool, $userId)
    {


        if (false === array_key_exists("sort", $pool)) {
            $pool['sort'] = "date_desc";
        }
        $hybridList = HybridListFactory::getUserWishListHybridList($pool, $userId);
        $info = $hybridList->execute();


        $model['bundle'] = [
            'general' => $info,
            'slice' => $hybridList->getControl('slice')->getModel(),
            'sort' => $hybridList->getControl('sort')->getModel(),
        ];

        return [
            'listBundle' => $model['bundle'],
        ];

    }
}