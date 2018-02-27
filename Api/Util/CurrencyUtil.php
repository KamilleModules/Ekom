<?php


namespace Module\Ekom\Api\Util;


use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Utils\E;

class CurrencyUtil
{
    public static function convertAmount($amount, $srcCurrency, $dstCurrency, $shopId = null)
    {
        if ($srcCurrency === $dstCurrency) {
            return $amount;
        }

        if (null === $shopId) {
            $shopId = E::getShopId();
        }

        $rates = CurrencyLayer::getExchangeRates([
            $srcCurrency,
            $dstCurrency,
        ], $shopId);

        $srcRate = $rates[$srcCurrency];
        $dstRate = $rates[$dstCurrency];
        $ratio =  $srcRate / $dstRate;

        return E::trimPrice($amount * $ratio);

    }
}