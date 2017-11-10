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
                    'rawTotal' => 0,
                    'items' => [],
                ];
            }
            $ret[$seller]['rawTotal'] += $item['priceLineRaw'];
            $ret[$seller]['items'][] = $item;
        }


        foreach ($ret as $seller => $item) {
            $ret[$seller]['total'] = E::price($item['rawTotal']);
            $taxHint = 0;
            Hooks::call("Ekom_Cart_getSellerTaxHint", $taxHint, $seller, $ret[$seller]["items"]);
            $ret[$seller]['taxHint'] = $taxHint;
        }
        return $ret;
    }


}