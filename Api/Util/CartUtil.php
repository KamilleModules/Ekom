<?php


namespace Module\Ekom\Api\Util;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class CartUtil
{

    /**
     * @param array $items
     * @return array of seller => info, with info having the following structure:
     *
     * - total: the total to display
     * - rawTotal: the internal total used for computation
     * - items: the items for the current seller
     *
     */
    public static function orderItemsBySeller(array $items)
    {
        $ret = [];

        foreach ($items as $item) {

            $seller = $item['seller'];

            if (false === array_key_exists($seller, $ret)) {
                $ret[$seller] = [
                    'total' => 0,
                    'rawTotal' => 0,
                    'items' => [],
                ];
            }

            $ret[$seller]['rawTotal'] += $item['rawLinePriceWithTax'];
            $ret[$seller]['items'][] = $item;
        }


        foreach ($ret as $k => $item) {
            $ret[$k]['total'] = E::price($item['rawTotal']);
        }
        return $ret;
    }


}