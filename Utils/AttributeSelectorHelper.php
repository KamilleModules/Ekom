<?php


namespace Module\Ekom\Utils;

use Kamille\Services\XLog;


/**
 * One widget in ekom is called productBox.
 * ProductBox displays the product info, including what we call an attribute selector.
 *
 *
 * The attribute selector displays all the possible variations of attributes values for a given reference.
 *
 * So for instance if we are on a card with two attributes: color and size, the attribute selector looks like this:
 *
 * ---------------
 * Color:
 *      red    green    blue
 * Size:
 *      4   6   8   10
 * ---------------
 *
 * Each attribute value being a button.
 * Now imagine the size 10 is only available for the blue color, how do you handle that?
 *
 * So for instance, we are on the green-8 variation, it looks like this:
 *
 * ---------------
 * Color:
 *      red    <green>    blue
 * Size:
 *      4   6   <8>   10
 * ---------------
 * Should size 10 button be active or inactive?
 *
 * The obvious answer is inactive, because there is no green-10 product, right?
 *
 * Okay, this means basically that for each button we need to determine it's state: active/inactive,
 * based on whether a corresponding reference exist or not.
 *
 * This class helps with that (I put this code in a separated class to ease testing).
 *
 *
 *
 *
 *
 *
 *
 */
class AttributeSelectorHelper
{
    public static function adaptProductWithAttributesToAttributesModel(array $items, $productId)
    {
        $productId = (int)$productId;
        $ret = [];

        //--------------------------------------------
        // LIST ALL POSSIBLE ATTR VALUES
        //--------------------------------------------
        $allAttr = [];
        $attr2Info = [];
        $attrName2Label = [];
        $p = null;
        foreach ($items as $item) {
            $attr = $item['attributes'];
            $sAttr = '';
            foreach ($attr as $at) {
                $allAttr[$at['name']][] = $at['value'];
                $sAttr .= $at['value'];
                $attrName2Label[$at['name']] = $at['name_label'];
            }
            if ((int)$item['product_id'] === $productId) {
                $p = $item;
            }
            $attr2Info[$sAttr] = [$item['reference'], $item['product_id'], $item['slug'], $item['active'], $item['quantity']];
        }
        foreach ($allAttr as $name => $values) {
            $allAttr[$name] = array_unique($values);
        }
        //--------------------------------------------
        // GET THE PRODUCT'S ATTR VALUES
        //--------------------------------------------
        if (null !== $p) {

            $productAttributes = [];
            foreach ($p['attributes'] as $info) {
                $productAttributes[$info['name']] = $info['value'];
            }

            //--------------------------------------------
            // COMPILE THE RESULTS PER ATTRIBUTE
            //--------------------------------------------

            foreach ($allAttr as $name => $values) {
                $theValues = [];


                foreach ($values as $value) {

                    $thisProductAttributes = $productAttributes;
                    $thisProductAttributes[$name] = $value;
                    $s = implode('', $thisProductAttributes);


                    $pId = 0;
                    $ref = 0;
                    $slug = 0;
                    $selected = "0";
                    $active = "0";
                    $quantity = "0";
                    $productUri = "";
                    $productAjaxUri = "";
                    $existence = "0";
                    if (array_key_exists($s, $attr2Info)) {
                        $existence = "1";
                        $pInfo = $attr2Info[$s];
                        list($ref, $pId, $slug, $active, $quantity) = $pInfo;
                        if ('' === $slug) {
                            $slug = $ref;
                        }

                        if ((int)$productId === (int)$pId) {
                            $selected = "1";
                        }
                        $productUri = E::link("Ekom_product", ['slug' => $slug]);
                        $productAjaxUri = E::link("Ekom_ajaxApi") . "?action=getProductInfo&id=" . $pId;

                    }


                    $theValues[] = [
                        "value" => $value,
                        "selected" => $selected,
                        "active" => $active,
                        "quantity" => $quantity,
                        "existence" => $existence,
                        "productUri" => $productUri,
                        "getProductInfoAjaxUri" => $productAjaxUri,
                        "product_id" => $pId,
                    ];
                }


                $ret[$name] = [
                    "label" => $attrName2Label[$name],
                    "values" => $theValues,
                ];
            }


        } else {
            XLog::error("[Ekom module] - AttributeSelectorHelper: the product $productId was not found for the given items");
            return false;
        }


        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Here is some code you can use to test the method above
     */
//    private function testRig()
//    {
//        $items = [
//            //--------------------------------------------
//            // RED
//            //--------------------------------------------
//            [
//                "product_id" => '100',
//                "reference" => 'r4',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '1',
//                        'name_label' => 'the color',
//                        'value' => 'red',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '4',
//                        'name_label' => 'the size',
//                        'value' => '4',
//                    ],
//                ],
//            ],
//            [
//                "product_id" => '101',
//                "reference" => 'r6',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '1',
//                        'name_label' => 'the color',
//                        'value' => 'red',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '6',
//                        'name_label' => 'the size',
//                        'value' => '6',
//                    ],
//                ],
//            ],
//            [
//                "product_id" => '102',
//                "reference" => 'r8',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '1',
//                        'name_label' => 'the color',
//                        'value' => 'red',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '8',
//                        'name_label' => 'the size',
//                        'value' => '8',
//                    ],
//                ],
//            ],
//            //--------------------------------------------
//            // GREEN
//            //--------------------------------------------
//            [
//                "product_id" => '103',
//                "reference" => 'g4',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '0',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '2',
//                        'name_label' => 'the color',
//                        'value' => 'green',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '4',
//                        'name_label' => 'the size',
//                        'value' => '4',
//                    ],
//                ],
//            ],
//            [
//                "product_id" => '104',
//                "reference" => 'g6',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '2',
//                        'name_label' => 'the color',
//                        'value' => 'green',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '6',
//                        'name_label' => 'the size',
//                        'value' => '6',
//                    ],
//                ],
//            ],
//            [
//                "product_id" => '105',
//                "reference" => 'g8',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '2',
//                        'name_label' => 'the color',
//                        'value' => 'green',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '8',
//                        'name_label' => 'the size',
//                        'value' => '8',
//                    ],
//                ],
//            ],
//            //--------------------------------------------
//            // BLUE
//            //--------------------------------------------
//            [
//                "product_id" => '106',
//                "reference" => 'b4',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '3',
//                        'name_label' => 'the color',
//                        'value' => 'blue',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '4',
//                        'name_label' => 'the size',
//                        'value' => '4',
//                    ],
//                ],
//            ],
//            [
//                "product_id" => '107',
//                "reference" => 'b6',
//                "slug" => '',
//                "active" => '0',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '3',
//                        'name_label' => 'the color',
//                        'value' => 'blue',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '6',
//                        'name_label' => 'the size',
//                        'value' => '6',
//                    ],
//                ],
//            ],
//            [
//                "product_id" => '108',
//                "reference" => 'b8',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '3',
//                        'name_label' => 'the color',
//                        'value' => 'blue',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '8',
//                        'name_label' => 'the size',
//                        'value' => '8',
//                    ],
//                ],
//            ],
//            [
//                "product_id" => '109',
//                "reference" => 'b10',
//                "slug" => '',
//                "active" => '1',
//                "quantity" => '50',
//                "attributes" => [
//                    [
//                        'name' => 'color',
//                        'attribute_id' => '1',
//                        'value_id' => '3',
//                        'name_label' => 'the color',
//                        'value' => 'blue',
//                    ],
//                    [
//                        'name' => 'size',
//                        'attribute_id' => '2',
//                        'value_id' => '10',
//                        'name_label' => 'the size',
//                        'value' => '10',
//                    ],
//                ],
//            ],
//        ];
//
//
//        $defaultProductId = 103;
//        a(AttributeSelectorHelper::adaptProductWithAttributesToAttributesModel($items, $defaultProductId));
//    }
}