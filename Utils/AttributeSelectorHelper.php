<?php


namespace Module\Ekom\Utils;

use Core\Services\A;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Util\UriUtil;
use Module\Ekom\Models\EkomModels;
use QuickPdo\QuickPdo;


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
     * @param $cardProducts , the ensemble of products belonging to the same card
     * @param $productId , the id of the product from which derives the default attributes combination
     * @see AttributeSelectorHelper::testRig()
     * @return array of attributes model.
     *          In short, here is what is returned: the productModifiersListModel.
     *          @see EkomModels::productModifiersListModel()
     *
     *
     *
     * See doc for more info
     *
     *          - value: the attribute value, formatted for the database
     *          - value_label: the attribute value, formatted for the humans
     *          - value_id: the attribute value id in the database
     *          - selected: bool whether or not the attribute value is part of the user selection
     *          - (DEPRECATED)quantity: if it's 0, indicate that this particular combination is out of stock.
     *                          Note: some products have quantity=-1, which means infinite.
     *                          We let the template interpret those values for themselves.
     *
     *                          Deprecation note: quantity brings a whole new set of problem when
     *                          you go into implementation details, I suggest we deprecate it
     *                          and let the user click on a button and see for herself (once
     *                          the page is refreshed) that the quantity is 0.
     *                          The implementation problem I had:
     *                          Suppose you have three attributes:
     *                              - country: Belgium
     *                              - date: 14 - 16
     *
     *                          Now imagine that the product with combination Belgium-14
     *                          is in stock with quantity=200,
     *                          but the combination Belgium-16 is out of stock.
     *
     *                          The idea is that the Belgium button is available,
     *                          the 14 button is available too,
     *                          but the 16 button is not available.
     *
     *                          It might be a good idea to implement this system if you have time
     *                          for this (but right now I don't)
     *
     *
     *
     *
     *
     *
     *
     *          - (DEPRECATED) possible: 1|0, whether or not the attribute value should be display.
     *
     *                              THIS IS DEPRECATED, since I don't see why we should give
     *                              to the template something that it's not supposed to display anyway!!!
     *
     *
     *                              For instance if country=china, we won't display city=Paris
     *                              In other words is the attribute visible for the user.
     *                              Note: level 1 (root) attributes always have possible=1
     *
     *
     *
     */
    public static function adaptProductWithAttributesToAttributesModel(array $cardProducts, $productId)
    {

        $productLayer = EkomApi::inst()->productLayer();
        $productId = (int)$productId;

        //--------------------------------------------
        // gathering all possible attributes, and more stuff
        //--------------------------------------------
        /**
         * Chains of possible:
         * since attributes are "supposedly" order by order asc
         * (note for the implementor: if they are not, you will have a problem)
         * then we can predict possible value chains (just by looking at the different attribute combinations
         * of the different cards), and thus say whether or not an attribute combination is possible.
         *
         * If it's not possible, it shouldn't be displayed.
         * The best example I have so far to illustrate this is:
         *
         * country: China - France
         * city: Beijing - Paris,
         *
         * When the user selects France, you don't want Beijing to be an option...
         *
         */
        $allAttributes = [];
        $selectedAttributeIds = [];
        $_values = [];
        $possibleChains = [];
        foreach ($cardProducts as $cardProduct) {
            $_productId = (int)$cardProduct['product_id'];
            $_qty = (int)$cardProduct['quantity'];
            $attributes = $cardProduct['attributes'];
            $possibleChains[$_productId] = '';
            foreach ($attributes as $index => $attributeInfo) {

                $attrId = $attributeInfo['attribute_id'];
                $valueId = $attributeInfo['value_id'];
                $possibleChains[$_productId] .= $valueId . '-';
                $selected = ($productId === $_productId) ? '1' : '0';
                $attributeInfo['selected'] = $selected;
                $attributeInfo['_product_id'] = $_productId;
                $attributeInfo['_qty'] = $_qty;
                $selectedAttributeIds[$index] = $attributeInfo["attribute_id"];


                /**
                 * eliminating potential doublons right away,
                 * but if the doublon is selected and the existing copy is not,
                 * the doublon replaces the existing copy (otherwise the user cannot
                 * select the item at all)
                 */
                if (
                    array_key_exists($attrId, $_values) &&
                    array_key_exists($valueId, $_values[$attrId])
                ) {

                    if ('0' === $selected) {
                        // the candidate doublon is not selected, so it doesn't matter,
                        // we can skip it
                        continue;
                    } else {
                        // the candidate doublon is selected, so it overrides the existing copy
                        foreach ($allAttributes[$index] as $k => $v) {
                            if ($valueId === $v['value_id'] && '0' === $v['selected']) {
                                $allAttributes[$index][$k] = $attributeInfo;
                                continue 2;
                            }
                        }
                    }
                }


                $_values[$attrId][$valueId] = true;
                $allAttributes[$index][] = $attributeInfo;
            }
        }


        //--------------------------------------------
        // adding the possible property
        //--------------------------------------------
        $level = 1;
        $curChain = "";
        $nextChain = "";
        $atLeastOneSelected = false;
        foreach ($allAttributes as $attrIndex => $attributes) {

            foreach ($attributes as $k => $attributeInfo) {

                $_productId = $attributeInfo['_product_id'];


                if (1 === $level) {
                    $attributes[$k]['possible'] = "1";
                } else {


                    $atLeastOneMatch = false;
                    foreach ($possibleChains as $possibleChain) {

                        $needle = $curChain . $attributeInfo['value_id'] . '-';
                        if (0 === strpos($possibleChain, $needle)) {
                            $atLeastOneMatch = true;
                            break;
                        }
                    }
                    if (true === $atLeastOneMatch) {
                        $attributes[$k]['possible'] = "1";
                    } else {
                        $attributes[$k]['possible'] = "0";
                        /**
                         * Comment the line below if you want to display the possible 0|1
                         * instead of getting rid of possible=0
                         */
                        unset($attributes[$k]);
                    }

                }
                if ('1' === $attributeInfo['selected']) {
                    $atLeastOneSelected = true;
                    $nextChain = $attributeInfo['value_id'] . '-';
                }


                if (array_key_exists($k, $attributes) && "1" === $attributes[$k]['possible']) {

                    $info = $productLayer->getLinkInfoByProductId($_productId);

                    /**
                     * Note: about this productUri
                     * Note: this uri might be a little buggy if there are many products as exposed in the intro.
                     * Todo: read the intro, and find the appropriate solution
                     * Note: for now I believe it will only work if the product has only one attribute (I'm in a rush sorry)...
                     */
                    $productUri = E::link("Ekom_productCardRefId", ['slug' => $info['cardSlug'], 'refId' => $info['refId']]);
                    $productAjaxUri = UriUtil::getProductBoxBaseAjaxUri($_productId);
                    $attributes[$k]['page_uri'] = $productUri;
                    $attributes[$k]['ajax_page_uri'] = $productAjaxUri;
                }
            }
            $curChain .= $nextChain;
            $allAttributes[$attrIndex] = $attributes;
            $level++;
        }


        $ret = [];
        $c = 0;
        foreach ($allAttributes as $attributes) {
            foreach ($attributes as $attributeInfo) {


                $selected = $attributeInfo['selected'];
                if (false === $atLeastOneSelected && 0 === $c++) {
                    $selected = "1";
                }

                $name = $attributeInfo["name"];
                if (false === array_key_exists($name, $ret)) {
                    $ret[$name] = [
                        "label" => $attributeInfo["attribute_label"],
                        "values" => [],
                    ];
                }

                $ret[$name]['values'][] = [
                    'value' => $attributeInfo['value'],
                    'value_label' => $attributeInfo['value_label'],
                    'value_id' => $attributeInfo['value_id'],
                    'selected' => $selected,
                    'page_uri' => $attributeInfo['page_uri'],
                    'ajax_page_uri' => $attributeInfo['ajax_page_uri'],
                    'product_id' => $attributeInfo['_product_id'],
                ];

                $ret[$name]['attribute_id'] = $attributeInfo['attribute_id'];
            }
        }

        return $ret;
    }


    public static function getSelectedAttributesByProductId(int $productId){
        return QuickPdo::fetchAll("
select 
a.name as attribute_name,
a.label as attribute_label,
v.value as value_name,
v.label as value_label

from ek_product_attribute a 
inner join ek_product_has_product_attribute h on h.product_attribute_id=a.id
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id
inner join ek_product_card_has_product_attribute hh on hh.product_attribute_id=a.id

where h.product_id=$productId

group by a.name

order by hh.order asc


        
        ");
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Here is some code you can use to test the adaptProductWithAttributesToAttributesModel
     * method above
     */
    private function testRig()
    {
        $items = [
            //--------------------------------------------
            // RED
            //--------------------------------------------
            [
                "product_id" => '100',
                "reference" => 'r4',
                "slug" => '',
                "active" => '1',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '1',
                        'attribute_label' => 'the color',
                        'value' => 'red',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '4',
                        'attribute_label' => 'the size',
                        'value' => '4',
                    ],
                ],
            ],
            [
                "product_id" => '101',
                "reference" => 'r6',
                "slug" => '',
                "active" => '1',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '1',
                        'attribute_label' => 'the color',
                        'value' => 'red',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '6',
                        'attribute_label' => 'the size',
                        'value' => '6',
                    ],
                ],
            ],
            [
                "product_id" => '102',
                "reference" => 'r8',
                "slug" => '',
                "active" => '1',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '1',
                        'attribute_label' => 'the color',
                        'value' => 'red',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '8',
                        'attribute_label' => 'the size',
                        'value' => '8',
                    ],
                ],
            ],
            //--------------------------------------------
            // GREEN
            //--------------------------------------------
            [
                "product_id" => '103',
                "reference" => 'g4',
                "slug" => '',
                "active" => '1',
                "quantity" => '0',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '2',
                        'attribute_label' => 'the color',
                        'value' => 'green',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '4',
                        'attribute_label' => 'the size',
                        'value' => '4',
                    ],
                ],
            ],
            [
                "product_id" => '104',
                "reference" => 'g6',
                "slug" => '',
                "active" => '1',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '2',
                        'attribute_label' => 'the color',
                        'value' => 'green',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '6',
                        'attribute_label' => 'the size',
                        'value' => '6',
                    ],
                ],
            ],
            [
                "product_id" => '105',
                "reference" => 'g8',
                "slug" => '',
                "active" => '1',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '2',
                        'attribute_label' => 'the color',
                        'value' => 'green',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '8',
                        'attribute_label' => 'the size',
                        'value' => '8',
                    ],
                ],
            ],
            //--------------------------------------------
            // BLUE
            //--------------------------------------------
            [
                "product_id" => '106',
                "reference" => 'b4',
                "slug" => '',
                "active" => '1',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '3',
                        'attribute_label' => 'the color',
                        'value' => 'blue',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '4',
                        'attribute_label' => 'the size',
                        'value' => '4',
                    ],
                ],
            ],
            [
                "product_id" => '107',
                "reference" => 'b6',
                "slug" => '',
                "active" => '0',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '3',
                        'attribute_label' => 'the color',
                        'value' => 'blue',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '6',
                        'attribute_label' => 'the size',
                        'value' => '6',
                    ],
                ],
            ],
            [
                "product_id" => '108',
                "reference" => 'b8',
                "slug" => '',
                "active" => '1',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '3',
                        'attribute_label' => 'the color',
                        'value' => 'blue',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '8',
                        'attribute_label' => 'the size',
                        'value' => '8',
                    ],
                ],
            ],
            [
                "product_id" => '109',
                "reference" => 'b10',
                "slug" => '',
                "active" => '1',
                "quantity" => '50',
                "attributes" => [
                    [
                        'name' => 'color',
                        'attribute_id' => '1',
                        'value_id' => '3',
                        'attribute_label' => 'the color',
                        'value' => 'blue',
                    ],
                    [
                        'name' => 'size',
                        'attribute_id' => '2',
                        'value_id' => '10',
                        'attribute_label' => 'the size',
                        'value' => '10',
                    ],
                ],
            ],
        ];


        $defaultProductId = 103;
        a(AttributeSelectorHelper::adaptProductWithAttributesToAttributesModel($items, $defaultProductId));
    }
}