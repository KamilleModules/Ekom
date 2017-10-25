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


    /**
     * Note: this method is just there so that I can find all the occurrences of it.
     * There should be only one occurrence of it.
     * But it's a key moment in the product details implementation:
     * the product details go from being separated (major/minor) to be merged.
     *
     * Actually, two times.
     *
     */
    public static function getMergedProductDetails(array $productDetails)
    {
        $majorDetailsParams = (array_key_exists('major', $productDetails)) ? $productDetails['major'] : [];
        $minorDetailsParams = (array_key_exists('minor', $productDetails)) ? $productDetails['minor'] : [];
        return array_merge($majorDetailsParams, $minorDetailsParams);
    }


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
     * - total: the total to display
     * - rawTotal: the internal total used for computation
     * - items: the items for the current seller
     *
     */
    public static function orderItemsBySeller(array $items)
    {
        $ret = [];
        $b2b = E::isB2b();

        foreach ($items as $item) {

            $seller = $item['seller'];

            if (false === array_key_exists($seller, $ret)) {
                $ret[$seller] = [
                    'taxApplies' => $item['taxApplies'],
                    'total' => 0,
                    'totalWithoutTax' => 0,
                    'totalWithTax' => 0,
                    'rawTotal' => 0,
                    'rawTotalWithoutTax' => 0,
                    'rawTotalWithTax' => 0,
                    'items' => [],
                ];
            }

            $ret[$seller]['rawTotalWithoutTax'] += $item['rawLinePriceWithoutTax'];
            $ret[$seller]['rawTotalWithTax'] += $item['rawLinePriceWithTax'];
            $ret[$seller]['items'][] = $item;
        }


//        foreach ($ret as $k => $item) {
//            $ret[$k]['totalWithoutTax'] = E::price($item['rawTotalWithoutTax']);
//            $ret[$k]['totalWithTax'] = E::price($item['rawTotalWithTax']);
//
//
//            // false === vat applies <-equals?-> no tax
//            if (true === $b2b || false === $ret[$k]['taxApplies']) {
//                $rawTotal = $item['rawTotalWithoutTax'];
//
//            } else {
//                $rawTotal = $item['rawTotalWithTax'];
//            }
//            $ret[$k]['rawTotal'] = $rawTotal;
//            $ret[$k]['total'] = E::price($rawTotal);
//        }
        return $ret;
    }


}