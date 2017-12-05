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
            $_items[] = [
                "time" => strtotime($item['date']),
                "label" => $item['label'],
                "priceRaw" => $price,
                "price" => E::price($price),
            ];
        }
        return [
            'totalRaw' => $total,
            'total' => E::price($total),
            'items' => $_items,
        ];
    }
}