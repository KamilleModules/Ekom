<?php


namespace Module\Ekom\Helper\Stats\Modules;


use Core\Services\A;
use Module\Ekom\Utils\EkomStatsUtil\EkomStatsUtil;

class NewsletterControllerModule
{

    public static function getModuleHandler()
    {
        return function ($dateStart, $dateEnd) {

            $template = "Ekom/All/Stats/OrdersAndGeneralStats/newsletter";
            $conf = [];


            //--------------------------------------------
            //
            //--------------------------------------------
            $options = [];

            $graph = EkomStatsUtil::create()
                ->prepare($dateStart, $dateEnd, $options)
                ->getGraph("newsletter");


            $rowsNbCustomers = [];
            $rowsNbVisitors = [];
            $rowsNbTotal = [];
            $totalNbCustomers = 0;
            $totalNbVisitors = 0;
            $total = 0;
            foreach ($graph as $date => $info) {
                $rowsNbCustomers[] = [$date, $info["nb_customer"]];
                $rowsNbVisitors[] = [$date, $info["nb_visitor"]];
                $rowsNbTotal[] = [$date, $info["nb_total"]];

                $totalNbCustomers += $info["nb_customer"];
                $totalNbVisitors += $info["nb_visitor"];
                $total += $info["nb_total"];
            }

            $conf['chart1'] = [
                "title" => "Dates de première inscription à la newsletter",
                "width" => "100%",
                "series" => [
                    'Total (' . $total . ')' => $rowsNbTotal,
                    'Par le bloc sur la homepage (' . $totalNbVisitors . ')' => $rowsNbVisitors,
                    'Par la page "monCompte" (' . $totalNbCustomers . ')' => $rowsNbCustomers,
                ],
            ];


            //--------------------------------------------
            // NEWSLETTER LIST
            //--------------------------------------------
            $conf['list'] = A::getMorphicListConfig("Ekom", "back/stats/newsletter_users", [
                "dateStart" => $dateStart,
                "dateEnd" => $dateEnd,
            ]);


//            $conf['chart3'] = [
//                'title' => "Distribution des états de commande",
//                'height' => "600",
//                'width' => "100%",
//                'labelColor' => "black",
//                'piePositionTop' => "50",
////                'piePositionLeft' => "60",
//                'radius' => "40",
//                'legend' => [
//                    'right' => 'auto',
//                    'bottom' => '0',
//                    'orient' => 'horizontal',
//                ],
//                'data' => $orderDistribution,
//                'dataColors' => $orderStatusLabels2Colors,
//            ];


            return [
                $template,
                $conf,
            ];
        };
    }
}