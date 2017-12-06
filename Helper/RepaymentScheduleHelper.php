<?php


namespace Module\Ekom\Helper;


use Bat\DateTool;
use Module\Ekom\Utils\E;

class RepaymentScheduleHelper
{


    /**
     * @param array $items , each of which having the following structure:
     * - date: the date (sql format: yyyy-mm-dd)
     * - label:
     * - price: the number,
     *          this will be internally converted using the current currency
     *
     * @return array
     */
    public static function createRepaymentScheduleByItems(array $items)
    {
        $total = 0;
        $_items = [];
        foreach ($items as $item) {
            $price = $item['price'];
            $total += $price;


            $item['time'] = strtotime($item['date']);
//            $item['label'] = $item['label'];
            $item['priceRaw'] = $price;
            $item['price'] = E::price($price);
            $_items[] = $item;
        }
        return [
            'totalRaw' => $total,
            'total' => E::price($total),
            'items' => $_items,
        ];
    }

    public static function getMensualRepaymentSchedule(array $payments, $startDate = null)
    {

        $nbPayments = count($payments);
        if (null === $startDate) {
            $startDate = date('Y-m-d');
        }

        $time = strtotime($startDate);

        $items = [];
        $i = 1;
        $total = 0;
        foreach ($payments as $payment) {
            $label = "Versement $i/$nbPayments";
            $items[] = [
                "time" => DateTool::getSameDayNextMonth($time, $i - 1),
                "label" => $label,
                "priceRaw" => $payment,
                "price" => E::price($payment),
            ];
            $i++;
            $total += $payment;
        }

        return [
            'totalRaw' => $total,
            'total' => E::price($total),
            'items' => $items,
        ];
    }
}