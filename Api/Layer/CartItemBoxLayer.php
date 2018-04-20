<?php


namespace Module\Ekom\Api\Layer;

use Core\Services\A;
use Core\Services\Hooks;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use Module\Ekom\Model\EkomModel;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\AttributeSelectorHelper;
use QuickPdo\QuickPdo;


/**
 * 2018-04-19
 *
 * This object represents a cart item.
 * @see EkomModels::cartItemBoxModel()
 *
 *
 *
 *
 *
 *
 */
class CartItemBoxLayer
{


    public static function getBox(string $productId, array $selectedProductDetails = [])
    {


        // note: product details might be able to change the core of the base query?
        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery(null, [
            "useTaxRuleConditionId" => true,
        ]);
        $sqlQuery->addField("p.weight, p.wholesale_price");
        $sqlQuery->addWhere("and p.id=$productId");

        $row = QuickPdo::fetch((string)$sqlQuery, $sqlQuery->getMarkers());
        self::sugarify($row);
        return $row;
    }


    public static function sugarify(array &$row, array $selectedProductDetails = [])
    {
        MiniProductBoxLayer::sugarify($row);

        $selectedAttributesInfo = AttributeSelectorHelper::getSelectedAttributesByProductId($row['product_id']);
        $selectedProductDetailsInfo = [];
        Hooks::call("Ekom_collectProductDetailsInfo", $selectedProductDetailsInfo, $selectedProductDetails);


        /**
         * We basically provide the ground for detailed information at the cart display level.
         */
        $row['selected_attributes_info'] = $selectedAttributesInfo;
        $row['selected_product_details_info'] = $selectedProductDetailsInfo;
        $row['selected_product_details'] = $selectedProductDetails; // map


        $row['tax_details'] = TaxLayer::getTaxDetailsInfoByTaxRuleConditionId($row['tax_rule_condition_id'], $row['base_price']);
        /**
         * as for now we only have ONE discount max per product, but in the future we could evolve...
         */
        $row['discount_details'] = [];
        if (true === $row['has_discount']) {
            $row['discount_details'][] = [
                "label" => $row['discount_label'],
                "type" => $row['discount_type'],
                "value" => $row['discount_value'],
            ];
        }

        $row['product_uri_with_details'] = $row['product_uri']; // if in doubt, recreate it from scratch
        if ($selectedProductDetails) {
            $row['product_uri_with_details'] .= "?" . http_build_query($selectedProductDetails);
        }


    }


}