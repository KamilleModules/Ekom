<?php


namespace Module\Ekom\Api\Util;


use Core\Services\Hooks;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Utils\E;
use Module\ThisApp\ThisAppConfig;

class CartUtil
{


    public static function generateTokenByProductIdMajorProductDetails($productId, array $majorDetails = [])
    {
        $token = (string)$productId;

        if (count($majorDetails) > 0) {
            ksort($majorDetails);
            $sDetails = implode('-', $majorDetails); // this will be enough for now, later we could use a hash
            $token .= '-' . $sDetails;
        }
        return $token;
    }


    /**
     * @param array $items
     * @return array of seller => info, with info having the following structure:
     *
     * - taxHint: int, a number indicating
     *                  the type of visual hint to display next to the price totals for every seller.
     *                  Whether or not the tax was globally applied.
     *
     * - total: the total to display
     * - totalRaw: the internal total used for computation
     * - taxAmountTotal: the total amount of tax for this seller
     * - taxAmountTotalRaw: the internal total of tax for this seller
     * - taxDetails: an array, each entry representing a tax group applied to at least one product for this seller.
     *              Each entry is an array of taxGroupName to item, each item being an array with the following structure:
     *              - taxGroupLabel: string, the tax group label
     *              - taxAmountTotalRaw: number, the cumulated amount coming from this tax group for this seller
     *              - taxAmountTotal: the formatted version of taxAmountTotalRaw
     *
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
                    /**
                     * taxHint is a number indicating
                     * the type of visual hint to display next to the price totals
                     * for every seller.
                     *
                     * - 0: none
                     * - 1: no tax (HT in french)
                     * - 2: with tax (TTC in french)
                     *
                     */
                    'taxHint' => 0,
                    'total' => 0,
                    'taxAmountTotalRaw' => 0,
                    'taxAmountTotal' => 0,
                    'totalRaw' => 0,
                    'taxDetails' => [],
                    'items' => [],
                ];
            }

            if (!array_key_exists($item['taxGroupName'], $ret[$seller]['taxDetails'])) {
                $ret[$seller]['taxDetails'][$item['taxGroupName']] = [
                    'taxGroupLabel' => $item['taxGroupLabel'],
                    'taxAmountTotalRaw' => 0,
                    'taxAmountTotal' => 0,
                ];
            }


            $ret[$seller]['taxDetails'][$item['taxGroupName']]['taxAmountTotalRaw'] += $item['taxAmount'];
            $ret[$seller]['taxAmountTotalRaw'] += $item['taxAmount'];
            $ret[$seller]['totalRaw'] += $item['priceLineRaw'];
            $ret[$seller]['items'][] = $item;
        }


        foreach ($ret as $seller => $item) {
            $ret[$seller]['total'] = E::price($item['totalRaw']);
            $ret[$seller]['taxAmountTotal'] = E::price($item['taxAmountTotalRaw']);

            $taxDetails = $ret[$seller]['taxDetails'];
            if ($taxDetails) {
                foreach ($taxDetails as $k => $v) {
                    $v['taxAmountTotal'] = E::price($v['taxAmountTotalRaw']);
                    $taxDetails[$k] = $v;
                }
                $ret[$seller]['taxDetails'] = $taxDetails;
            }

            $taxHint = 0;
            Hooks::call("Ekom_Cart_getSellerTaxHint", $taxHint, $seller, $ret[$seller]["items"]);
            $ret[$seller]['taxHint'] = $taxHint;
        }
        return $ret;
    }


}