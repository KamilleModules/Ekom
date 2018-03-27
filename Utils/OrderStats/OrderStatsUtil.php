<?php


namespace Module\Ekom\Utils\OrderStats;


use Bat\MathTool;
use Module\Ekom\Api\Layer\OrderLayer;

class OrderStatsUtil
{


    /**
     * @todo-ling: implement with date range...
     */
    public static function getUserReport($userId, $startDate = null, $endDate = null, callable $dateFunc = null)
    {

        if (null === $dateFunc) {
            $dateFunc = function ($date) {
                return $date;
            };
        }

        $stats = OrderLayer::getBasicStatsByUser($userId);
        $nbFailingOrders = OrderLayer::countFailingOrderByUserId($userId);
        $nbOrderTotal = $stats['nb_total_order'];
        $nbOrderWithoutCoupon = $stats['nb_order_without_coupon'];
        $nbOrdersWithCoupon = $nbOrderTotal - $nbOrderWithoutCoupon;
        $nbSuccessOrders = $nbOrderTotal - $nbFailingOrders;
        $successFailurePercent = MathTool::getPercentagesByKeyValue([
            "success" => $nbSuccessOrders,
            "failure" => $nbFailingOrders,
        ], ' %');


        $pCoupons = MathTool::getPercentagesByKeyValue([
            "with" => $nbOrdersWithCoupon,
            "without" => $nbOrderWithoutCoupon,
        ], ' %');


        $paymentStats = OrderLayer::getPaymentMethodStats($userId);
        $pPayment = MathTool::getPercentagesByKeyValue($paymentStats, ' %');


        return [
            "Nombre de commandes total" => $stats['nb_total_order'],
            "Date de la première commande" => call_user_func($dateFunc, $stats['min_date']),
            "Date de la dernière commande" => call_user_func($dateFunc, $stats['max_date']),

            "Pourcentage de commandes sans erreur" => $successFailurePercent['success'],
            "Pourcentage de commandes avec au moins une erreur" => $successFailurePercent['failure'],

            "Nombre de commandes sans erreur" => $nbSuccessOrders,
            "Nombre de commandes avec au moins une erreur" => $nbFailingOrders,

            "Pourcentage commandes avec réduction" => $pCoupons['with'],
            "Pourcentage commandes sans réduction" => $pCoupons['without'],
            "Nombre achats commandes avec réduction" => $nbOrdersWithCoupon,
            "Nombre achats commandes sans réduction" => $nbOrderWithoutCoupon,


            "Panier moyen" => $stats['avg_amount'],
            "Panier max" => $stats['max_amount'],
            "Panier min" => $stats['min_amount'],

            "Quantité moyenne" => $stats['quantity_avg'],
            "Quantité max" => $stats['quantity_max'],
            "Quantité min" => $stats['quantity_min'],


            // a key starting with underscore indicates an intention of visual separator
            "_1" => "",
            // moyen de paiement
            "Pourcentage de commandes par virement" => $pPayment['transfer'] ?? "0 %",
            "Pourcentage de commandes par carte bleue 1x" => $pPayment['credit_card_wallet1x'] ?? "0 %",
            "Pourcentage de commandes par carte bleue 4x" => $pPayment['credit_card_wallet4x'] ?? "0 %",
            //
            "Nombre de commandes par virement" => $paymentStats['transfer'] ?? "0",
            "Nombre de commandes par carte bleue 1x" => $paymentStats['credit_card_wallet1x'] ?? "0",
            "Nombre de commandes par carte bleue 4x" => $paymentStats['credit_card_wallet4x'] ?? "0",
        ];
    }


}