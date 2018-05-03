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
 * should put the selected product details map.
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
    public static function getProductDetailsString(array $selectedProductDetailsMap)
    {
        $s = "";
        $c = 0;
        foreach ($selectedProductDetailsMap as $k => $v) {
            if (0 !== $c) {
                $s .= ";;";
            }
            $s .= $k . "==" . $v;
            $c++;
        }
        return $s;
    }

    public static function updateProductDetailsCacheString(int $productReferenceId, array $selectedProductDetailsMap)
    {
        $s = self::getProductDetailsString($selectedProductDetailsMap);
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
            $selectedProductDetailsMap = [];
            Hooks::call("Ekom_ProductDetails_collectProductDetails", $selectedProductDetailsMap, $productRefId);
            self::updateProductDetailsCacheString($productRefId, $selectedProductDetailsMap);

        }
    }

}