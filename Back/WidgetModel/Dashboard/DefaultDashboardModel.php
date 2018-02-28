<?php


namespace Module\Ekom\Back\WidgetModel\Dashboard;


use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomStatsUtil\EkomStatsUtil;
use Module\EkomCartTracker\Api\Layer\EkomCartTrackerCartLayer;
use Module\EkomUserTracker\Api\Layer\UserTrackerLayer;

class DefaultDashboardModel{


    public static function getModel(){

        $dateStart = date('Y-m-d H:i:s');
        $dateEnd = date('Y-m-d H:i:s', strtotime("+2 day"));


        $dateEnd = "2017-12-13 23:59:59";
        $dateStart = "2017-12-13 00:00:00";

        $dateStart = null;
        $dateEnd = null;

        $shopId = 1;



        $o = EkomStatsUtil::create()
            ->prepare($dateStart, $dateEnd, [
                'currency' => "EUR",
                'shopId' => $shopId,
            ]);



        $info = $o->getRevenues();
        list($amount, $count) = $info;

        $ret = [
            "revenue" => E::price($amount), // CA HT
            "nbOrders" => $count,
            "avgCart" => E::price($amount / $count),
            "visitors" => $o->getNbIp(), // total
            "conversionRate" => $o->getConversionRate() . ' %',
            "netProfit" => E::price($o->getNetProfit()),
            "visitorsLast30Min" => UserTrackerLayer::getNbConnectedUsers(30 * 60),
            "activeCartsLast30Min" => EkomCartTrackerCartLayer::getNbUniqueCarts(date('Y-m-d H:i:s', strtotime("-30 minutes"))),
            "nbPreparingOrders" => $o->getNbPreparingOrder(),
            "nbAbandonedCarts" => $o->getNbAbandonedCarts(),
            "nbOutOfStockProducts" => ProductLayer::getNbOutOfStockProducts($shopId),
            "nbNewCustomers" => $o->getNbNewCustomers(),
            "nbNewNewsletterSubscribers" => $o->getNbNewNewsletterSubscribers(),
            "nbTotalNewsletterSubscribers" => $o->getNbTotalNewsletterSubscribers(),

        ];
        return $ret;
    }

}