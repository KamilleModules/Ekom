<?php


namespace Module\Ekom\Api\Layer;

use Core\Services\A;
use Core\Services\Hooks;
use Module\Ekom\Api\Util\ProductQueryBuilderUtil;
use Module\Ekom\Model\EkomModel;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\AttributeSelectorHelper;
use Module\Ekom\Utils\E;
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


    public static function getBox(int $productReferenceId)
    {


        // note: product details might be able to change the core of the base query?
        $sqlQuery = ProductQueryBuilderUtil::getBaseQuery([
            "useTaxRuleConditionId" => true,
            "useAttributesString" => true,
        ]);
        $sqlQuery->addField("p.weight, p.wholesale_price");
        $sqlQuery->addWhere("and pr.id=$productReferenceId");


//        az(__FILE__, $sqlQuery->getSqlQuery(), $sqlQuery->getMarkers());

        $row = QuickPdo::fetch((string)$sqlQuery, $sqlQuery->getMarkers());
        self::sugarify($row);


        Hooks::call("Ekom_CartItemBox_decorateItem", $row);

        return $row;
    }


    public static function sugarify(array &$row)
    {
        MiniProductBoxLayer::sugarify($row);

        $selectedAttributesInfo = AttributeSelectorHelper::getSelectedAttributesByProductId($row['product_id']);

        Hooks::call("Ekom_CartItemBox_decorateCartItemBoxModel", $row);


        /**
         * We basically provide the ground for detailed information at the cart display level.
         */
        $row['selected_attributes_info'] = $selectedAttributesInfo;
        $row['selected_product_details_info'] = $row['selected_product_details_info'] ?? [];

        /**
         * Rebuilding productDetailsMap from productDetailsInfo
         */
        $productDetailsMap = [];
        foreach ($row['selected_product_details_info'] as $info) {
            $productDetailsMap[$info['detail_name']] = $info['value_name'];
        }
        $row['selected_product_details'] = $row['selected_product_details'] ?? $productDetailsMap;
        $selectedProductDetails = $row['selected_product_details'];

        if ($row['tax_rule_condition_id']) {
            $row['tax_details'] = TaxLayer::getTaxDetailsInfoByTaxRuleConditionId($row['tax_rule_condition_id'], $row['base_price']);
        } else {
            $row['tax_details'] = [];
        }

        $taxAmount = $row['sale_price'] - $row['base_price'];
        $row['tax_amount'] = $taxAmount;
        $row['tax_amount_formatted'] = E::price($taxAmount);

        /**
         * as for now we only have ONE discount max per product, but in the future we could evolve...
         * So for now I just compute discount using a subtraction (do the changes if you use more than one discount
         * per product
         */
        $discountAmountPerUnit = $row['base_price'] - $row['real_price'];

        $row['discount_details'] = [];
        if (true === $row['has_discount']) {
            $row['discount_details'][] = [
                "label" => $row['discount_label'],
                "type" => $row['discount_type'],
                "value" => $row['discount_value'],
                "amount" => $discountAmountPerUnit,
            ];
        }

        $row['product_uri_with_details'] = $row['product_uri']; // if in doubt, recreate it from scratch
        if ($selectedProductDetails) {
            $row['product_uri_with_details'] .= "?" . http_build_query($selectedProductDetails);
        }


    }


}