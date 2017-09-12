<?php


namespace Module\Ekom\Api\Util;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
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


    /**
     * This method was originally created to update a cart price
     * for products with complex price system (like the EkomEvents module).
     *
     * For instance, a composite price, depending on which options the user chose.
     *
     * In this case, depending on which price you target: the base price or the sale price,
     * and which side (without tax or with tax), you'll have to update the other prices as well.
     *
     * As for now, here are all the price types (not including the raw version for readability):
     *
     * - priceWithTax
     * - priceWithoutTax
     * - salePriceWithTax
     * - salePriceWithoutTax
     *
     * So, if you choose to change the sale price, there is not much to do, but if you change the price,
     * you need to recompute the discounts, or at least that's the idea.
     *
     * This is not currently implemented because discounts could be anything and potentially could be
     * hard to reverse engineer.
     *
     * Therefore, I recommend (as long as this comment persists) to target only the sale price (not the price).
     *
     *
     * @param $target :
     *
     * - priceWithoutTax
     * - priceWithTax
     * - salePriceWithoutTax
     * - salePriceWithTax
     *
     *
     * This method updates, in the model, the changes defined by the target
     * and newValue parameters.
     *
     * @return void
     * @throws \Exception when something wrong happens
     *
     *
     */
    public static function updateCartItemPrice($target, $newPrice, array &$cartItemInfo)
    {
        $taxRatio = $cartItemInfo['taxRatio'];
        if ('salePriceWithoutTax' === $target) {

            $cartItemInfo['rawSalePriceWithoutTax'] = E::trimPrice($newPrice);
            $cartItemInfo['rawSalePriceWithTax'] = E::trimPrice($newPrice * $taxRatio);
            $cartItemInfo['salePriceWithoutTax'] = E::price($cartItemInfo['rawSalePriceWithoutTax']);
            $cartItemInfo['salePriceWithTax'] = E::price($cartItemInfo['rawSalePriceWithTax']);

        } elseif ('salePriceWithTax' === $target) {

            $cartItemInfo['rawSalePriceWithoutTax'] = E::trimPrice($newPrice / $taxRatio);
            $cartItemInfo['rawSalePriceWithTax'] = E::trimPrice($newPrice);
            $cartItemInfo['salePriceWithoutTax'] = E::price($cartItemInfo['rawSalePriceWithoutTax']);
            $cartItemInfo['salePriceWithTax'] = E::price($cartItemInfo['rawSalePriceWithTax']);

        }
        elseif ('priceWithoutTax' === $target) {

            $cartItemInfo['rawPriceWithoutTax'] = E::trimPrice($newPrice);
            $cartItemInfo['rawPriceWithTax'] = E::trimPrice($newPrice * $taxRatio);

        }
        else {
            throw new EkomApiException("This target is not handled: $target");
        }

    }
}