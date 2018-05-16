<?php


namespace Module\Ekom\Helper\Stats\Modules;


use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\OrderStatusLayer;
use Module\Ekom\Utils\EkomStatsUtil\EkomStatsUtil;

class OrdersAndProfitControllerModule
{

    public static function getModuleHandler()
    {
        return function ($dateStart, $dateEnd) {

            $template = "Ekom/All/Stats/OrdersAndGeneralStats/orders_and_profit";
            $conf = [];


            //--------------------------------------------
            //
            //--------------------------------------------
            $orderDistribution = OrderLayer::getOrdersDistributionByRange($dateStart, $dateEnd);
            $orderStatusLabels2Colors = OrderStatusLayer::getOrderStatusLabel2BgColor();

            $options = [];

            $graph = EkomStatsUtil::create()
                ->prepare($dateStart, $dateEnd, $options)
                ->getGraph("nbOrdersAndNbProductsAndNetProfit");


            $rowsNbOrders = [];
            $rowsRevenueProfit = [];
            $rowsNbItems = [];
            $rows = [];
            foreach ($graph as $date => $info) {
                $rowsNbOrders[] = [$date, $info["count"]];
                $rowsRevenueProfit[] = [$date, $info["sum"]];
                $rowsNbItems[] = [$date, $info["sumNbItems"]];
            }

            $conf['chart1'] = [
                "title" => "Nombre de commandes et quantités commandées",
                "series" => [
                    'Nombre de commandes' => $rowsNbOrders,
                    'Quantité commandée' => $rowsNbItems,
                ],
            ];

            $conf['chart2'] = [
                "title" => "Ventes",
                'series' => [
                    'Ventes: EUR' => $rowsRevenueProfit,
                ],
                'toolTipFormatter' => <<<EEE
<span style="color: #c2c2c2; font-size: 0.8em;">{key}</span><br>{value}€
EEE
            ];


            $conf['chart3'] = [
                'title' => "Distribution des états de commande",
                'height' => "600",
                'width' => "100%",
                'labelColor' => "black",
                'piePositionTop' => "50",
//                'piePositionLeft' => "60",
                'radius' => "40",
                'legend' => [
                    'right' => 'auto',
                    'bottom' => '0',
                    'orient' => 'horizontal',
                ],
                'data' => $orderDistribution,
                'dataColors' => $orderStatusLabels2Colors,
            ];


            return [
                $template,
                $conf,
            ];
        };
    }
}