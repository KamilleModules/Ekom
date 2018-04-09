<?php


namespace Module\Ekom\Helper\Stats\Modules;


use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\OrderStatusLayer;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomStatsUtil\EkomStatsUtil;
use Module\EkomUserTracker\Api\Layer\EkomUserTrackerLayer;

class CustomerAccountsControllerModule
{

    public static function getModuleHandler()
    {
        return function ($dateStart, $dateEnd) {

            $template = "Ekom/All/Stats/OrdersAndGeneralStats/customer_accounts";
            $conf = [];


            $util = EkomStatsUtil::create()->prepare($dateStart, $dateEnd);


            $nbCustomers = $util->getNbNewCustomers();
            $nbCustomersOrderingFirstDay = $util->getNbNewCustomerOrderingFirstDay();
            if ($nbCustomers > 0) {
                $nbCustomersOrderingFirstDayPercent = E::trimPercent($nbCustomersOrderingFirstDay / $nbCustomers * 100);
            } else {
                $nbCustomersOrderingFirstDayPercent = 0;
            }


            $info = EkomUserTrackerLayer::getAccountCreationStats([
                'dateRange' => [
                    $dateStart,
                    $dateEnd,
                ],
            ]);
            $creationAccountTotal = $info['total'];
            $creationAccountSuccess = $info['success'];
            $creationAccountFailure = $creationAccountTotal - $creationAccountSuccess;
            if ($creationAccountTotal > 0) {

                $creationAccountFailurePercent = E::trimPercent($creationAccountFailure / $creationAccountTotal * 100);
            } else {
                $creationAccountFailurePercent = 0;
            }


            $conf['veryFlat'] = [
                "Visiteurs bloqués à la phase d'inscription : " => "$creationAccountFailure / $creationAccountTotal ($creationAccountFailurePercent%)",
                "Visiteurs ayant passé une commande dès leur inscription : " => "$nbCustomersOrderingFirstDay / $nbCustomers ($nbCustomersOrderingFirstDayPercent%)",
                "Total de comptes créés : " => $nbCustomers,
            ];

            $chart1 = $util->getGraph("newCustomers");
            $realChart1 = [];
            foreach ($chart1 as $k => $v) {
                $realChart1[] = [$k, $v];
            }

            $conf['chart1'] = [
                "title" => "Comptes créés",
                "series" => [
                    "Comptes créés" => $realChart1,
                ],
            ];


            return [
                $template,
                $conf,
            ];
        };
    }
}