<?php


namespace Module\Ekom\Back\WidgetModel\Dashboard;


use Module\Ekom\Api\Layer\ProductLayer;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomStatsUtil\EkomStatsUtil;
use Module\EkomCartTracker\Api\Layer\EkomCartTrackerCartLayer;
use Module\EkomUserTracker\Api\Layer\UserTrackerLayer;

class DefaultDashboardModel
{


    public static function getModel($dateStart = null, $dateEnd = null, array $options = [])
    {

        $options = array_replace([
            "mode" => 'default', // default||ajax
            "graph" => "revenue",
        ], $options);

        $mode = $options['mode'];
        $graph = $options['graph'];


        if (null === $dateStart && null === $dateEnd) {
            $dateStart = date('Y-m-d H:i:s');
            $dateEnd = date('Y-m-t H:i:s'); // last day of month
        } else {
            if (null === $dateStart) {
                $dateStart = date('Y-m-d H:i:s');
            }
            if (null === $dateEnd) {
                $dateEnd = date('Y-m-d H:i:s');
            }
        }


        $o = EkomStatsUtil::create()
            ->prepare($dateStart, $dateEnd, [
                'currency' => "EUR",
            ]);


        $info = $o->getRevenues();
        list($amount, $count) = $info;
        $avgCart = (0 === $count) ? 0 : $amount / $count;



        $ajaxRet = [
            "revenue" => E::price($amount), // CA HT
            "nbOrders" => $count,
            "avgCart" => E::price($avgCart),
            "visitors" => $o->getNbIp(), // total
            "conversionRate" => $o->getConversionRate() . ' %',
            "netProfit" => E::price($o->getNetProfit()),
            "visitorsLast30Min" => UserTrackerLayer::getNbConnectedUsers(30 * 60),
            "activeCartsLast30Min" => EkomCartTrackerCartLayer::getNbUniqueCarts(date('Y-m-d H:i:s', strtotime("-30 minutes"))),
            "nbPreparingOrders" => $o->getNbPreparingOrder(),
            "nbAbandonedCarts" => $o->getNbAbandonedCarts(),
            "nbOutOfStockProducts" => ProductLayer::getNbOutOfStockProducts(),
            "nbNewCustomers" => $o->getNbNewCustomers(),
            "nbNewNewsletterSubscribers" => $o->getNbNewNewsletterSubscribers(),
            "nbTotalNewsletterSubscribers" => $o->getNbTotalNewsletterSubscribers(),
            "graph" => self::toGraphData($o->getGraph($graph)),

        ];

        if ("ajax" === $mode) {
            return $ajaxRet;
        }

        $ret = $ajaxRet;
        $format = "Y-m-d";
        $ret["startDate"] = date($format, strtotime($dateStart));
        $ret["endDate"] = date($format, strtotime($dateEnd));
        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function toGraphData(array $graph)
    {

        $ret = [];
        foreach ($graph as $date => $value) {
            $ret[] = [
                strtotime($date) * 1000,
                $value,
            ];
        }
        return $ret;
    }
}