<?php


namespace Module\Ekom\Helper;

use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\AttributeLayer;
use Module\Ekom\Api\Layer\CategoryLayer;


/**
 *
 * Syntax reminder for db insertion/extraction
 * ======================
 *
 * The inline version of the rules uses this syntax:
 *
 *
 * - syntax: <rule> (<ruleSep> <rule>)*
 * - ruleSep: the diamond symbol (<>)
 * - rule: <quantity> <:> <criterion>
 * - quantity: number
 * - criterion: <criteria> (<criteriaSep> <criteria>)*
 * - criteriaSep: sharp symbol (#)
 * - criteria: <type> <=> <values>
 * - type: string, one of:
 *      - product
 *      - card
 *      - attribute
 *      - category
 *      - manufacturer
 *      - seller
 * - values: csv of ids of the given type
 *
 *
 *
 *
 * A product matches the syntax only if all rules match
 * A product matches a rule if it matches all the criterion of the rule
 *
 *
 *
 *
 *
 */
class ConditionRulesHelper
{
    public static function uncompile($value)
    {

        $ruleSep = '<>';
        $criteriaSep = '#';
        $ret = [];
        if ($value) {

            $rules = explode($ruleSep, $value);
            foreach ($rules as $rule) {
                $p = explode(':', $rule, 2);
                $quantity = $p[0];
                $criterion = $p[1];
                $allCriteria = explode($criteriaSep, $criterion);
                $_criterion = [];
                foreach ($allCriteria as $criteria) {
                    $q = explode('=', $criteria, 2);
                    $type = $q[0];
                    $values = $q[1];
                    $aValues = explode(',', $values);
                    $count = count($aValues);

                    $_criterion[] = [$type, $values, $count];
                }


                $_rule = [
                    "quantity" => $quantity,
                    "criterion" => $_criterion,
                ];

                $ret[] = $_rule;
            }
        }
        return $ret;
    }


    public static function evaluateRule(array $rule, array $cartModel)
    {

//        a("evaluating rule", $rule);
        $nbItemsMatching = 0;
        $quantity = $rule['quantity'];
        $criterion = $rule['criterion'];
        $items = $cartModel['items'];
        foreach ($items as $item) {
            if (true === self::cartItemMatchesCriterion($item, $criterion)) {
                $nbItemsMatching += $item['cart_quantity'];
//                a("passed");
            } else {
//                a("failed");
            }
        }

        return ($nbItemsMatching >= $quantity);

    }

    /**
     * @param array $cartItem
     * @see EkomModels::cartItemBoxModel()
     * @param array $criterion
     *
     * @return bool, whether the given cartItem matches the criteria
     */
    public static function cartItemMatchesCriterion(array $cartItem, array $criterion)
    {
        foreach ($criterion as $criteria) {
            list($type, $value) = $criteria;

            // values of string
            $values = explode(',', $value);
            /**
             * If in the gui you add an empty rule, it will create a nasty record, something like this:
             * - 0: "",
             * so we want to clean those up
             */
            $values = array_filter($values);

//            a("evaluating type: $type with value $value for " . substr($cartItem['label'], 0, 10));
            switch ($type) {
                case "product":
                    $productId = $cartItem['product_id'];
                    if (false === in_array($productId, $values, true)) {
                        return false;
                    }
                    break;
                case "card":
                    $productCardId = $cartItem['product_card_id'];
                    if (false === in_array($productCardId, $values, true)) {
                        return false;
                    }
                    break;
                case "attribute":
                    /**
                     * Valid if the current item contains AT LEAST ONE OF the given attributes
                     */
                    $isValid = false;
                    $attributeIds = AttributeLayer::getAttributeIdsByProductId($cartItem['product_id']);
                    if (empty($values)) {
                        self::debug("empty attributes, will pass", true);
                        return true;
                    }
                    foreach ($attributeIds as $attributeId) {
                        if (in_array($attributeId, $values, true)) {
                            $isValid = true;
                            break;
                        }
                    }
                    if (false === $isValid) {
                        self::debug("attribute criteria invalid");
                        return false;
                    } else {
                        self::debug("attribute criteria valid", true);
                    }
                    break;
                case "category":
                    /**
                     * Valid if at least one of the current item's categories belongs to the categories given in
                     * the conditions.
                     */
                    $isValid = false;
                    $categoryIds = CategoryLayer::getCategoryIdsByProductCardId($cartItem['product_card_id']);
                    if (empty($values)) {
                        self::debug("empty categories, will pass", true);
                        return true;
                    }
                    foreach ($categoryIds as $categoryId) {
                        if (in_array($categoryId, $values, true)) {
                            $isValid = true;
                            break;
                        }
                    }
                    if (false === $isValid) {
                        self::debug("categories criteria invalid");
                        return false;
                    } else {
                        self::debug("categories criteria valid", true);
                    }
                    break;
                case "manufacturer":
                    $manufacturerId = $cartItem['manufacturer_id'];
                    if (false === in_array($manufacturerId, $values, true)) {
                        return false;
                    }
                    break;
                case "seller":
                    $sellerId = $cartItem['seller_id'];
                    if (false === in_array($sellerId, $values, true)) {
                        return false;
                    }
                    break;
                default:
                    XLog::error("Unknown criteria type: $type, condition denied");
                    return false;
                    break;
            }
        }
        return true;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private static function debug(string $m, bool $isSuccess = false)
    {
        return;
        $color = (true === $isSuccess) ? "green" : "red";
        echo '<span style="color: ' . $color . '">' . $m . '</span>';
    }
}