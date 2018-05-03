<?php


namespace Module\Ekom\Helper;

use Core\Services\Hooks;
use Module\Ekom\Api\Object\ProductReference;
use QuickPdo\QuickPdo;


/**
 * Implementation of product details is module dependent,
 * and therefore there is no one-fit-all way of doing it.
 *
 * However, in the ek_product_reference table,
 * we have the _product_details field, which is where modules
 * should put the pretty version of the selected product details map
 * (using labels instead of names).
 *
 *
 * The primary goal being to have quick access to the product details from a category list.
 *
 *
 * This allows Ekom to basically show the product details on the list items (in the front)
 * without having to know the implementation details of each module.
 * It's fast (because it's just one field to fetch: no extra joins or extra sql logic required).
 *
 * The counter part is that your gui must always recreate this cache field, otherwise
 * your app becomes unsync.
 *
 *
 */
class ProductDetailsHelper
{

    /**
     * @param array $selectedProductDetailsMap
     * @return string, the string to insert in the "ek_product_reference._product_details" cache field.
     */
    public static function getProductDetailsString(array $prettySelectedProductDetailsMap)
    {
        $s = "";
        $c = 0;
        foreach ($prettySelectedProductDetailsMap as $k => $v) {
            if (0 !== $c) {
                $s .= ";;";
            }
            $s .= $k . "==" . $v;
            $c++;
        }
        return $s;
    }

    public static function updateProductDetailsCacheString(int $productReferenceId, array $prettySelectedProductDetailsMap)
    {
        $s = self::getProductDetailsString($prettySelectedProductDetailsMap);
        ProductReference::getInst()->update([
            "_product_details" => $s,
        ], [
            "id" => $productReferenceId,
        ]);
    }


    /**
     * This method will update all _product_details field.
     */
    public static function refreshDatabase()
    {
        $rows = QuickPdo::fetchAll("select id from ek_product_reference", [], \PDO::FETCH_COLUMN);
        foreach ($rows as $productRefId) {
            $prettySelectedProductDetailsMap = [];
            Hooks::call("Ekom_ProductDetails_collectPrettyProductDetails", $prettySelectedProductDetailsMap, $productRefId);
            self::updateProductDetailsCacheString($productRefId, $prettySelectedProductDetailsMap);
        }
    }

    public static function productDetailsCacheStringToArray(string $productDetailsCacheString)
    {
        $ret = [];
        $lines = explode(";;", $productDetailsCacheString);
        foreach ($lines as $line) {
            $q = explode('==', $line, 2);
            if (2 === count($q)) {
                $ret[$q[0]] = $q[1];
            }
        }
        return $ret;
    }

}