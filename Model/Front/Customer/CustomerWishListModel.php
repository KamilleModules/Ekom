<?php


namespace Module\Ekom\Model\Front\Customer;


use Module\Ekom\Helper\SqlQueryHelper;
use Module\Ekom\HybridList\HybridListFactory;
use Module\Ekom\SqlQueryWrapper\EkomProductListSqlQueryWrapper;
use SqlQueryWrapper\Plugins\SqlQueryWrapperSortPlugin;


class CustomerWishListModel
{

    public static function getModel(int $userId)
    {

        $sqlQuery = SqlQueryHelper::getUserWishListSqlQuery($userId);

        $wrapper = EkomProductListSqlQueryWrapper::create();

        /**
         * @var $sortPlugin SqlQueryWrapperSortPlugin
         */
        $sortPlugin = $wrapper->getPlugin("sort");
        $sortPlugin->setDefaultSort("uhpr.date_added_desc");
        $sortPlugin->prependSortItems([
            "uhpr.date_added_asc" => "Date d'ajout ascendante",
            "uhpr.date_added_desc" => "Date d'ajout descendante",
        ]);



        $wrapper->setSqlQuery($sqlQuery)->prepare();

        return [
            "listWrapper" => $wrapper,
        ];
    }

    public static function getModelDeprecated(array $pool, $userId)
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