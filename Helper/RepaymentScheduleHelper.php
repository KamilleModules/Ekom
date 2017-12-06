<?php


namespace Module\Ekom\Helper;


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

        // todo...
        $time = $startTime;
        if (null === $time) {
            $time = time();
        }


        $p = E::trimPrice($total / $nbRepayments);
        $p2 = $total - ($nbRepayments - 1) * $p;
        $items = [];
        for ($i = 0; $i < $nbRepayments - 1; $i++) {
            $label = "";
            if (0 === $i) {
                $label = "1er versement";
            } elseif (1 === $i) {
                $label = "2ème versement";
            } elseif (2 === $i) {
                $label = "3ème versement";
            }

            $items[] = [
                "time" => DateTool::getSameDayNextMonth($time, $i),
                "label" => $label,
                "priceRaw" => $p,
                "price" => E::price($p),
            ];
        }

        if (2 === $i) {
            $label = "3ème et dernier versement";
        } elseif (3 === $i) {
            $label = "4ème et dernier versement";
        }

        $items[] = [
            "time" => DateTool::getSameDayNextMonth($time, $i),
            "label" => $label,
            "priceRaw" => $p2,
            "price" => E::price($p2),
        ];

        return [
            'totalRaw' => $total,
            'total' => E::price($total),
            'items' => $items,
        ];
    }
}