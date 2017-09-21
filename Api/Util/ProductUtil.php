<?php


namespace Module\Ekom\Api\Util;


use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Utils\E;

class ProductUtil
{

    /**
     *
     * Will inject the following in the boxModel:
     * - rawPriceWithTax
     * - rawPriceWithoutTax
     *
     * At least one of the first two parameters must be provided
     *
     *
     * @param null $newRawPriceWithoutTax
     * @param null $newRawPriceWithTax
     * @param array $boxModel
     * @throws EkomApiException
     */
    public static function updateProductPrice($newRawPriceWithoutTax = null, $newRawPriceWithTax = null, array &$boxModel)
    {

        if (
            null === $newRawPriceWithoutTax &&
            null === $newRawPriceWithTax
        ) {
            throw new EkomApiException("at least one of the two first arguments (i.e. newRawPriceWithoutTax, newRawPriceWithTax) must be provided");
        }

        $taxRatio = $boxModel['taxRatio'];
        if (null === $newRawPriceWithoutTax) {
            $newRawPriceWithoutTax = E::trimPrice($newRawPriceWithTax / $taxRatio);
        }
        if (null === $newRawPriceWithTax) {
            $newRawPriceWithTax = E::trimPrice($newRawPriceWithoutTax * $taxRatio);
        }
        $boxModel['rawPriceWithoutTax'] = $newRawPriceWithoutTax;
        $boxModel['rawPriceWithTax'] = $newRawPriceWithTax;
    }
}