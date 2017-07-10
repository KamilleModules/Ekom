<?php


namespace Module\Ekom\Utils;

use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;


/**
 * One widget in ekom is called productBox.
 * ProductBox displays the product info, including what we call an attribute selector.
 *
 *
 * The attribute selector displays all the possible variations of attributes values for a given reference.
 *
 *
 * Multiple approaches are possible.
 * My first approach was the following:
 *
 * Approach 1
 * =================
 *
 * Let's say we are on a card with two attributes: color and size, the attribute selector looks like this:
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
 *
 *
 * Unfortunately there is a problem with this approach: imagine we have only two products:
 * green-8 and blue-6.
 * With the current active/inactive system, if inactive means the user cannot click on the button,
 * then we cannot pass from green-8 to blue-6 because they don't have any attribute in common,
 * so we need another approach.
 *
 *
 * Approach 2
 * =================
 * In this approach, the order in which attributes are chosen by the user matters.
 * The first attribute must be selected first, and contains all possible choices,
 * THEN only, once the first attribute is selected, the list of all possible "second level" attributes
 * is displayed, and so on...
 *
 * So basically, we first display all the available colors (and they are all active):
 *
 * Color:
 *      <green>    blue
 *
 *
 * And then, depending on whether the user selects the green or the blue attribute, we display the corresponding
 * sizes, so for instance if the user selects green, we display size 8, and if the user selects color blue,
 * then we display size 6.
 *
 * This way we can workaround the limitations of approach 1.
 *
 * Also, every time a (active) button is clicked (no matter what level), it selects an existing product,
 * so that there is no way the user is misguided (same intent as approach 1).
 *
 *
 *
 *
 * =============
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

    /**
     *
     * Return the current state of the card attributes, depending on the selected product.
     *
     *
     * This function returns the card attributes as they should be displayed by the template.
     *
     * As said above, the card attributes are in a specific state, depending on
     * which product is representing the card (which by default is stored in the ek_shop_has_product_card table,
     * but can be dynamically changed by the user via the gui of the card).
     *
     * And so the array returned by this function represents that state.
     *
     * @param $cardProducts, the ensemble of products belonging to the same card
     * @param $productId, the id of the product from which derives the default attributes combination
     * @return array of attributes model, see doc for more info
     *
     */
    public static function adaptProductWithAttributesToAttributesModel(array $cardProducts, $productId)
    {

        $productId = (int)$productId;
        $ret = [];

        //--------------------------------------------
        // LIST ALL POSSIBLE ATTR VALUES
        //--------------------------------------------
        $allAttr = [];
        $attr2Info = [];
        $attrName2Id = [];
        $attrName2Label = [];
        $attrValue2Info = []; // [value_label, value_id]
        $p = null;
        foreach ($cardProducts as $item) {
            $attr = $item['attributes'];
            $sAttr = '';
            foreach ($attr as $at) {
                $allAttr[$at['name']][] = $at['value'];
                $sAttr .= $at['value'];
                $attrName2Label[$at['name']] = $at['name_label'];
                $attrName2Id[$at['name']] = $at['attribute_id'];
                $attrValue2Info[$at['value']] = [$at['value_label'], $at['value_id']];
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
            $productLayer = EkomApi::inst()->productLayer();

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

//                        $productUri = E::link("Ekom_product", ['slug' => $slug]);

                        $info = $productLayer->getLinkInfoByProductId($pId);


                        /**
                         * Note: about this productUri
                         * Note: this uri might be a little buggy if there are many products as exposed in the intro.
                         * Todo: read the intro, and find the appropriate solution
                         * Note: for now I believe it will only work if the product has only one attribute (I'm in a rush sorry)...
                         */
                        $productUri = E::link("Ekom_productCardRef", ['slug' => $info['cardSlug'], 'ref' => $info['ref']]);
                        $productAjaxUri = E::link("Ekom_ajaxApi") . "?action=getProductInfo&id=" . $pId;

                    }

                    list($valueLabel, $valueId) = $attrValue2Info[$value];

                    $theValues[] = [
                        "value" => $value,
                        "value_label" => $valueLabel,
                        "value_id" => $valueId,
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
                    /**
                     * Note: the attribute_id value is an extension of the original productBox model.
                     * It was added as a "courtesy" for cart methods (some cart methods want to
                     * access the attribute_id and using the productLayer methods is
                     * handy for them...)
                     */
                    "attribute_id" => $attrName2Id[$name],
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