<?php


namespace Module\Ekom\Api\Util;


use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Utils\E;

class CartUtil
{


    public static function getDetailsByBoxModel(array $boxModel)
    {
        if (
            array_key_exists("productCartDetails", $boxModel) &&
            array_key_exists("productCartDetailsParams", $boxModel)
        ) {
            return [
                $boxModel['productCartDetails'],
                $boxModel['productCartDetailsParams'],
            ];
        }
        return [
            [],
            [],
        ];
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
     * This method updates, in the model, the changes defined by the target
     * and newValue parameters.
     *
     *
     * @return void
     * @throws \Exception when something wrong happens
     *
     *
     */
    public static function updateCartItemPrice($newPrice, array &$cartItemInfo)
    {


        $target = 'priceWithoutTax';
        $taxRatio = $cartItemInfo['taxRatio'];
        if (
            'salePriceWithoutTax' === $target ||
            'salePriceWithTax' === $target
        ) {


            if ('salePriceWithoutTax' === $target) {
                $cartItemInfo['rawSalePriceWithoutTax'] = E::trimPrice($newPrice);
                $cartItemInfo['rawSalePriceWithTax'] = E::trimPrice($newPrice * $taxRatio);
            } else {
                $cartItemInfo['rawSalePriceWithTax'] = E::trimPrice($newPrice);
                $cartItemInfo['rawSalePriceWithoutTax'] = E::trimPrice($newPrice / $taxRatio);
            }


            $cartItemInfo['salePriceWithoutTax'] = E::price($cartItemInfo['rawSalePriceWithoutTax']);
            $cartItemInfo['salePriceWithTax'] = E::price($cartItemInfo['rawSalePriceWithTax']);
            if (true === $cartItemInfo['isB2B']) {
                $cartItemInfo['rawSalePrice'] = $cartItemInfo['rawSalePriceWithoutTax'];
                $cartItemInfo['salePrice'] = $cartItemInfo['salePriceWithoutTax'];
            } else {
                $cartItemInfo['rawSalePrice'] = $cartItemInfo['rawSalePriceWithTax'];
                $cartItemInfo['salePrice'] = $cartItemInfo['salePriceWithTax'];
            }


        } elseif (
            'priceWithoutTax' === $target ||
            'priceWithTax' === $target
        ) {

            if ('priceWithoutTax' === $target) {
                $cartItemInfo['rawPriceWithoutTax'] = E::trimPrice($newPrice);
                $cartItemInfo['rawPriceWithTax'] = E::trimPrice($newPrice * $taxRatio);
            } else {
                $cartItemInfo['rawPriceWithTax'] = E::trimPrice($newPrice);
                $cartItemInfo['rawPriceWithoutTax'] = E::trimPrice($newPrice / $taxRatio);
            }

            $badges = [];
            $productId = $cartItemInfo['product_id'];
            list($salePriceWithoutTax, $salePriceWithTax) = EkomApi::inst()->discountLayer()->applyDiscountsByProductId($productId, $cartItemInfo['rawPriceWithoutTax'], $cartItemInfo['rawPriceWithTax'], $badges);
            $cartItemInfo['rawSalePriceWithoutTax'] = $salePriceWithoutTax;
            $cartItemInfo['rawSalePriceWithTax'] = $salePriceWithTax;
            $cartItemInfo['priceWithoutTax'] = E::price($cartItemInfo['rawPriceWithoutTax']);
            $cartItemInfo['priceWithTax'] = E::price($cartItemInfo['rawPriceWithTax']);
            $cartItemInfo['salePriceWithoutTax'] = E::price($salePriceWithoutTax);
            $cartItemInfo['salePriceWithTax'] = E::price($salePriceWithTax);


            if (true === $cartItemInfo['isB2B']) {
                $cartItemInfo['rawPrice'] = $cartItemInfo['rawPriceWithoutTax'];
                $cartItemInfo['price'] = $cartItemInfo['priceWithoutTax'];
                $cartItemInfo['rawSalePrice'] = $cartItemInfo['rawSalePriceWithoutTax'];
                $cartItemInfo['salePrice'] = $cartItemInfo['salePriceWithoutTax'];
            } else {
                $cartItemInfo['rawPrice'] = $cartItemInfo['rawPriceWithTax'];
                $cartItemInfo['price'] = $cartItemInfo['priceWithTax'];
                $cartItemInfo['rawSalePrice'] = $cartItemInfo['rawSalePriceWithTax'];
                $cartItemInfo['salePrice'] = $cartItemInfo['salePriceWithTax'];
            }


        } else {
            throw new EkomApiException("This target is not handled: $target");
        }

    }
}