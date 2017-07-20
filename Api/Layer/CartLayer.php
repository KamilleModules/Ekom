<?php


namespace Module\Ekom\Api\Layer;


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\CartLocalStore;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomLinkHelper;
use Module\Ekom\Utils\ProductIdToUniqueProductIdAdaptor\ProductIdToUniqueProductIdAdaptor;
use QuickPdo\QuickPdo;


/**
 * About the cart:
 * ==================
 * - allowedExtraArgs: an array of arguments that the modules can add to the cart session storage (and re-use later
 *          for their own needs).
 *          For instance, the EkomCardCombination module uses this mechanism to identify/memorize the configuration
 *          of the product added to the cart.
 *
 *
 *
 */
class CartLayer
{


    public static $cartKey = 'ekom.cart';


    /**
     * @var CartLocalStore
     */
    private $cartLocalStore;

    private $_cartModel; // cache
    private $_miniCartModel; // cache


    /**
     *
     * This method was created to give js a mean to access session data (the
     * cart products quantities).
     *
     * @return array, the current session data for the current shop.
     */
//    public function getCartProduct2Quantities()
//    {
//        $this->initSessionCart();
//        $shopId = ApplicationRegistry::get("ekom.shop_id");
//        $ret = [];
//        foreach ($_SESSION[CartLayer::$cartKey][$shopId]['items'] as $id => $item) {
//            $ret[$id] = $item['quantity'];
//        }
//        return $ret;
//    }
//
//    public function getProductQuantity($productId, $default = 0)
//    {
//        $this->initSessionCart();
//        $shopId = ApplicationRegistry::get("ekom.shop_id");
//        if (array_key_exists($productId, $_SESSION[CartLayer::$cartKey][$shopId]['items'])) {
//            return $_SESSION[CartLayer::$cartKey][$shopId]['items'][$productId]['quantity'];
//        }
//        return $default;
//    }

    public function getItemsExtraArgs($productId, $argName, $default = null)
    {
        $productId = (int)$productId;
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $items = $_SESSION[CartLayer::$cartKey][$shopId]['items'];
        foreach ($items as $item) {
            if ((int)$item['id'] === $productId) {
                if (
                    array_key_exists("extraArgs", $item) &&
                    array_key_exists($argName, $item['extraArgs'])
                ) {
                    return $item['extraArgs'][$argName];
                }
            }
        }
        return $default;

    }


    public function addItems(array $productId2Qty)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");

        foreach ($productId2Qty as $productId => $qty) {

            $productId = (string)$productId;
            $alreadyExists = false;
            foreach ($_SESSION[CartLayer::$cartKey][$shopId]['items'] as $k => $item) {
                if ((string)$item['id'] === $productId) {
                    $_SESSION[CartLayer::$cartKey][$shopId]['items'][$k]['quantity'] += $qty;
                    $alreadyExists = true;
                    break;
                }
            }

            if (false === $alreadyExists) {
                $_SESSION[CartLayer::$cartKey][$shopId]['items'][] = [
                    "quantity" => $qty,
                    "id" => $productId,
                ];
            }
        }


        $this->writeToLocalStore();
    }

    public function addItem($qty, $productId, array $extraArgs = [])
    {

//        $upid = $this->getUniqueProductId($productId, $complementaryId);


        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $this->sanitizeExtraArgs($extraArgs);


        $stopPropagation = false;
        $hookParams = [
            'shopId' => $shopId,
            'stopPropagation' => $stopPropagation,
            'quantity' => $qty,
            'productId' => $productId,
            'extraArgs' => $extraArgs,
        ];
        Hooks::call("Ekom_cart_addItemBefore", $hookParams);


        if (false === $hookParams['stopPropagation']) {


            $alreadyExists = false;
            foreach ($_SESSION[CartLayer::$cartKey][$shopId]['items'] as $k => $item) {
                if ((string)$item['id'] === $productId) {
                    $_SESSION[CartLayer::$cartKey][$shopId]['items'][$k]['quantity'] += $qty;
                    $alreadyExists = true;
                    break;
                }
            }

            if (false === $alreadyExists) {

                $arr = [
                    "quantity" => $qty,
                    "id" => $productId,
                ];

                if ($extraArgs) {
                    $arr['extraArgs'] = $extraArgs;
                }

                $_SESSION[CartLayer::$cartKey][$shopId]['items'][] = $arr;
            }
        }

        // modules might have change the session even if this method didn't add the item.
        $this->writeToLocalStore();
    }

    public function removeItem($productId)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $productId = (string)$productId;
        foreach ($_SESSION[CartLayer::$cartKey][$shopId]['items'] as $k => $item) {
            if ((string)$item['id'] === $productId) {
                unset($_SESSION[CartLayer::$cartKey][$shopId]['items'][$k]);
                break;
            }
        }

        $this->writeToLocalStore();
    }


    /**
     *
     * Set the quantity for a given product in the cart,
     * with respect of the acceptOutOfStockOrders directive,
     * and return the number of that products put in the cart.
     *
     *
     * @return false|true|int,
     *          if false, a problem occurred, you can get the error with the errors array.
     *          if true, it means the quantity has been added to the cart.
     *          if int, represents the quantity that has been put into the cart.
     *                  Note: if acceptOutOfStockOrders is true, then any quantity will be accepted.
     *                  If acceptOutOfStockOrders is false, then if there is 7 products left and you order 10,
     *                  it will return 7.
     *
     */
    public function updateItemQuantity($productId, $newQty, array &$errors = [])
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $productId = (string)$productId;

        if (false !== ($remainingQty = EkomApi::inst()->productLayer()->getProductQuantity($productId))) {


            $remainingQty = (int)$remainingQty;
            $newQty = (int)$newQty;
            if ($newQty < 0) {
                $newQty = 0;
            }

            $maxQty = $newQty;
            $acceptOutOfStockOrders = E::conf("acceptOutOfStockOrders", false);

            if (false === $acceptOutOfStockOrders && $newQty > $remainingQty && -1 !== $remainingQty) {
                $newQty = $remainingQty;
            }

            $alreadyExists = false;
            foreach ($_SESSION[CartLayer::$cartKey][$shopId]['items'] as $k => $item) {
                if ((string)$item['id'] === $productId) {
                    $_SESSION[CartLayer::$cartKey][$shopId]['items'][$k]['quantity'] = $newQty;
                    $alreadyExists = true;
                    break;
                }
            }

            if (false === $alreadyExists) {
                $_SESSION[CartLayer::$cartKey][$shopId]['items'][] = [
                    "quantity" => $newQty,
                    "id" => $productId,
                ];
            }


            $this->writeToLocalStore();


            if ($maxQty === $newQty) {
                return true;
            }
            return $newQty;

        } else {
            XLog::error("[Ekom module] - CartLayer.updateItemQuantity: cannot access the product quantity for product $productId");
            $errors[] = "internal problem, please check the logs or contact the webmaster";
        }
        return false;
    }


    public function prepareUserCart()
    {
        if (null !== ($userId = SessionUser::getValue('id'))) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            if (false === array_key_exists(CartLayer::$cartKey, $_SESSION) || false === array_key_exists($shopId, $_SESSION[CartLayer::$cartKey])) {
                // the user doesn't have a session cart yet, we create it from the localStorage if any.

                $userCart = $this->getCartLocalStore()->getUserCart($userId, $shopId);
                $_SESSION[CartLayer::$cartKey][$shopId] = $userCart;

            } else {
                // the user already has a cart in session, she will use it, we don't need to do anything
            }
        } else {
            XLog::error("[Ekom module] - CartLayer.prepareUserCart: SessionUser not connected or doesn't have an id. sessionDump: " . ArrayToStringTool::toPhpArray($_SESSION));
        }
    }


    public function getCartModel(array $options = null)
    {
        return $this->doGetCartModel($options);
    }

    public function getMiniCartModel(array $options = null)
    {
        return $this->doGetCartModel($options);
    }





//    public function tryAddCouponByCode($code)
//    {
//        $this->initSessionCart();
//        $shopId = ApplicationRegistry::get("ekom.shop_id");
//        return $_SESSION[CartLayer::$cartKey][$shopId]['coupons'];
//    }

    public function getCouponBag()
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        return $_SESSION[CartLayer::$cartKey][$shopId]['coupons'];
    }


    /**
     * @param array $bag , array of coupon ids
     * @return $this
     */
    public function setCouponBag(array $bag)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $_SESSION[CartLayer::$cartKey][$shopId]['coupons'] = $bag;
        return $this;
    }


    public function clean()
    {
        $_SESSION[CartLayer::$cartKey] = [];
    }

    public function getContext()
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        return $_SESSION[CartLayer::$cartKey][$shopId];
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function initSessionCart()
    {
        EkomApi::inst()->initWebContext();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        if (false === array_key_exists(CartLayer::$cartKey, $_SESSION)) {
            $_SESSION[CartLayer::$cartKey] = [];
        }
        if (false === array_key_exists($shopId, $_SESSION[CartLayer::$cartKey])) {

            $defaultCart = [
                'items' => [],
                'coupons' => [],
            ];

            Hooks::call("Ekom_cart_decorateDefaultCart", $defaultCart);
            $_SESSION[CartLayer::$cartKey][$shopId] = $defaultCart;
        }
    }

    private function doGetCartModel(array $options = null)
    {
        if (null === $options) {
            $useEstimateShippingCosts = true;
            $items = null;
        } else {
            $useEstimateShippingCosts = (array_key_exists("useEstimateShippingCosts", $options) && true === $options['useEstimateShippingCosts']);
            $items = (array_key_exists('items', $options)) ? $options["items"] : null;
        }

        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $model = [];
        $modelItems = [];
        $totalQty = 0;
        $linesTotalWithoutTax = 0;
        $linesTotalWithTax = 0;

        if (null === $items) {
            $items = $_SESSION[CartLayer::$cartKey][$shopId]['items'];
        }



        $isB2b = ('b2b' === EkomApi::inst()->configLayer()->getBusinessType()) ? true : false;
        //--------------------------------------------
        // CALCULATING LINE PRICES AND TOTAL
        //--------------------------------------------
        foreach ($items as $item) {

            $id = $item['id'];

            if (false !== ($it = $this->getCartItemInfo($id))) {

                $qty = $item['quantity'];


                if (false === array_key_exists('errorCode', $it)) {

                    $it['quantity'] = $qty;
                    $totalQty += $qty;

//                $linePriceWithoutTax = $qty * $it['rawSalePriceWithoutTax'];
//                $linePriceWithTax = $qty * $it['rawSalePriceWithTax'];
                    $linePriceWithoutTax = $qty * $it['rawDiscountedPriceWithoutTax'];
                    $linePriceWithTax = $qty * $it['rawDiscountedPriceWithTax'];

//                $linesTotalWithoutTax += $linePriceWithoutTax;
//                $linesTotalWithTax += $linePriceWithTax;
                    $linesTotalWithoutTax += $linePriceWithoutTax;
                    $linesTotalWithTax += $linePriceWithTax;


//                $it['linePriceWithoutTax'] = E::price($linePriceWithoutTax);
//                $it['linePriceWithTax'] = E::price($linePriceWithTax);
                    $it['linePriceWithoutTax'] = E::price($linePriceWithoutTax);
                    $it['rawLinePriceWithoutTax'] = $linePriceWithoutTax;
                    $it['linePriceWithTax'] = E::price($linePriceWithTax);
                    $it['rawLinePriceWithTax'] = $linePriceWithTax;

                    if (true === $isB2b) {
                        $it['linePrice'] = $it['linePriceWithoutTax'];
                    } else {
                        $it['linePrice'] = $it['linePriceWithTax'];
                    }

//                $attrValues = [];
//                foreach ($it['attributes'] as $v) {
//                    $attrValues[] = $v['value'];
//                }
//                $it['attributeValues'] = $attrValues;

                    $modelItems[] = $it;
                } else {
                    XLog::error("[Ekom module] - CartLayer.doGetCartModel: errorCode: " . $it['errorCode'] . ", msg: " . $it['errorMessage']);
                }
            }
        }

//        echo '<hr>';
//        az($items);

        $taxAmount = $linesTotalWithTax - $linesTotalWithoutTax;


        $model['isB2B'] = $isB2b;
        $model['totalQuantity'] = $totalQty;
        $model['items'] = $modelItems;
//        $model['linesTotalWithoutTax'] = E::price($linesTotalWithoutTax);
//        $model['linesTotalWithTax'] = E::price($linesTotalWithTax);
        $model['linesTotalWithoutTax'] = E::price($linesTotalWithoutTax);
        $model['rawLinesTotalWithoutTax'] = $linesTotalWithoutTax;
        $model['linesTotalWithTax'] = E::price($linesTotalWithTax);
        $model['rawLinesTotalWithTax'] = $linesTotalWithTax;
        $model['taxAmount'] = E::price($taxAmount);
        $model['rawTaxAmount'] = $taxAmount;
        if (true === $isB2b) {
            $model['linesTotal'] = $model['linesTotalWithoutTax'];
            $model['rawLinesTotal'] = $model['linesTotalWithoutTax'];
        } else {
            $model['linesTotal'] = $model['linesTotalWithTax'];
            $model['rawLinesTotal'] = $model['linesTotalWithTax'];
        }


        //--------------------------------------------
        // adding/rechecking coupons
        //--------------------------------------------
        $couponApi = EkomApi::inst()->couponLayer();
        $validCoupons = [];


        $details = $couponApi->applyCouponBag($linesTotalWithoutTax, $linesTotalWithTax, "beforeShipping", $this->getCouponBag(), $validCoupons);
//        EkomApi::inst()->cartLayer()->setCouponBag($validCoupons);

        $cartTotalRaw = $details['rawDiscountPrice'];
        $cartTotalRawWithTax = $details['rawDiscountPriceWithTax'];

        $model['rawCartTotalWithoutTax'] = $cartTotalRaw;
        $model['rawCartTotalWithTax'] = $cartTotalRawWithTax;

        $model['cartTotalWithoutTax'] = $details['discountPrice'];
        $model['cartTotalWithTax'] = $details['discountPriceWithTax'];

        $model['totalSavingWithoutTax'] = $details['totalSaving'];
        $model['totalSavingWithTax'] = $details['totalSavingWithTax'];
        $model['rawTotalSavingWithoutTax'] = $details['rawTotalSaving'];
        $model['rawTotalSavingWithTax'] = $details['rawTotalSavingWithTax'];

        $model['coupons'] = $details['coupons'];
        $model['hasCoupons'] = (count($details['coupons']) > 0);


        if (true === $isB2b) {
            $model['cartTotal'] = $model['cartTotalWithoutTax'];
            $model['rawCartTotal'] = $model['rawCartTotalWithoutTax'];
            $model['totalSaving'] = $model['totalSavingWithoutTax'];
        } else {
            $model['cartTotal'] = $model['cartTotalWithTax'];
            $model['rawCartTotal'] = $model['rawCartTotalWithTax'];
            $model['totalSaving'] = $model['totalSavingWithTax'];
        }


        //--------------------------------------------
        // ADDING CARRIER INFORMATION
        //--------------------------------------------
        if (true === $useEstimateShippingCosts) {

            /**
             * we have basically two cases: either the user is connected, or not.
             * If the user is not connected, the application chooses its own heuristics
             * and returns an estimated shipping cost.
             *
             * If the user is connected and has a shipping address, the user's shipping address
             * is used for the base of calculating the estimated shipping cost.
             *
             */
            $carrierGroups = EkomApi::inst()->carrierLayer()->estimateShippingCosts($items);
            $model['carrierSections'] = $carrierGroups;
            $allShippingCosts = $carrierGroups['totalShippingCost'];


            $model['estimatedTotalShippingCost'] = E::price($allShippingCosts);
            $model['estimatedOrderGrandTotalWithoutTax'] = E::price($cartTotalRaw + $allShippingCosts);
            $model['estimatedOrderGrandTotalWithTax'] = E::price($cartTotalRawWithTax + $allShippingCosts);

            if (true === $isB2b) {
                $model['estimatedOrderGrandTotal'] = $model['estimatedOrderGrandTotalWithoutTax'];
            } else {
                $model['estimatedOrderGrandTotal'] = $model['estimatedOrderGrandTotalWithTax'];
            }
        }


        //--------------------------------------------
        // MODULES
        //--------------------------------------------
        Hooks::call("Ekom_CartLayer_decorate_mini_cart_model", $model);

        return $model;
    }

    private function getCartItemInfo($pId)
    {
        $pId = (int)$pId;
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");

        return A::cache()->get("Module.Ekom.Api.Layer.CartLayer.getCartItemInfo.$shopId.$langId.$pId", function () use ($pId, $shopId, $langId) {


            $b = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($pId, $shopId, $langId, true);
            if (array_key_exists('errorCode', $b)) {
                XLog::error("[Ekom module] - CartLayer.getCartItemInfo: product not found or request failed with product id: $pId");
                return $b;
            } else {


                $row = QuickPdo::fetch("
select 
p.slug as product_slug,
c.slug as card_slug,            
l.slug as card_default_slug,
l.product_card_id,
b.reference
            
from ek_shop_has_product_lang p            
inner join ek_product b on b.id=p.product_id
inner join ek_product_card_lang l on l.product_card_id=b.product_card_id and l.lang_id=p.lang_id
inner join ek_shop_has_product_card_lang c on c.shop_id=p.shop_id and c.product_card_id=b.product_card_id and c.lang_id=p.lang_id            
            
where 
b.id=$pId
and p.shop_id=$shopId            
and p.lang_id=$langId     

            ");


                $productCardId = $row['product_card_id'];
                $productSlug = $row['reference'];
                $cardSlug = ('' !== $row['card_slug']) ? $row['card_slug'] : $row['card_default_slug'];
//a($b);
//echo'<hr>';

                $attr = $b['attributes'];
                $zeAttr = [];
                foreach ($attr as $name => $info) {
                    $values = $info['values'];
                    foreach ($values as $val) {
                        if ('1' === $val['selected']) {
                            $zeAttr[] = [
                                "attribute_id" => $info['attribute_id'],
                                "label" => $info['label'],
                                "value" => $val['value'],
                            ];
                            break;
                        }
                    }
                }


                $mainImage = "";
                $imageSmall = "";
                if (count($b['images']) > 0) {
                    $arr = current($b['images']);
                    $mainImage = $arr['thumb']; // small?
                    $imageSmall = $arr['small']; // small?
                }


                return array_replace($b, [
                    'product_id' => $b['product_id'],
                    'label' => $b['label'],
                    'description' => $b['description'],
                    'stock_quantity' => $b['quantity'],
                    'ref' => $b['ref'],
                    'weight' => $b['weight'],
                    'uri' => E::link("Ekom_product", ['slug' => $productSlug]),
                    'remove_uri' => EkomLinkHelper::getUri("removeProductFromCart", $pId),
                    'update_qty_uri' => EkomLinkHelper::getUri("updateCartProduct", $pId),
                    'uri_card' => E::link("Ekom_productCard", ['slug' => $cardSlug]),
                    'uri_card_with_ref' => E::link("Ekom_productCardRef", ['slug' => $cardSlug, 'ref' => $b["ref"]]),
                    'product_card_id' => $productCardId,
                    'attributes' => $zeAttr,
                    'attributeDetails' => $zeAttr,
                    'price' => $b['price'],
                    'rawPrice' => $b['rawPrice'],
                    'salePrice' => $b['salePrice'],
                    'rawSalePrice' => $b['rawSalePrice'],
                    'discountedPriceWithoutTax' => $b['discountedPriceWithoutTax'],
                    'rawDiscountedPriceWithoutTax' => $b['rawDiscountedPriceWithoutTax'],
                    'discountedPriceWithTax' => $b['discountedPriceWithTax'],
                    'rawDiscountedPriceWithTax' => $b['rawDiscountedPriceWithTax'],
//                    'salePriceWithTax' => $b['salePriceWithTax'],
//                    'salePriceWithoutTax' => $b['salePriceWithoutTax'],
                    'image' => $mainImage,
                    'imageSmall' => $imageSmall,

                    'stockType' => $b['stockType'],
                    'stockText' => $b['stockText'],
                    'taxDetails' => $b['taxDetails'],

//                    'rawSalePriceWithoutTax' => $b['rawSalePriceWithoutTax'],
//                    'rawSalePriceWithTax' => $b['rawSalePriceWithTax'],
                ]);

            }

        }, [
            "ek_shop_has_product_card_lang",
            "ek_shop_has_product_card",
            "ek_product_card_lang",
            "ek_product_card",
            "ek_shop",
            "ek_product_has_product_attribute",
            "ek_product_attribute_lang",
            "ek_product_attribute_value_lang",
            "ek_product",
            "ekomApi.image.product",
            "ekomApi.image.productCard",
        ]);
    }

    private function writeToLocalStore()
    {
        if (true === SessionUser::isConnected()) {
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            if (null !== ($userId = SessionUser::getValue('id'))) {
                $this->getCartLocalStore()->saveUserCart($userId, $shopId, $_SESSION[CartLayer::$cartKey][$shopId]);
            } else {
                XLog::error("[Ekom module] - CartLayer: in shop#$shopId, this user doesn't have an id: " . ArrayToStringTool::toPhpArray($_SESSION));
            }
        }
    }


    /**
     * @return CartLocalStore
     */
    private function getCartLocalStore()
    {
        if (null === $this->cartLocalStore) {
            $this->cartLocalStore = new CartLocalStore();
        }
        return $this->cartLocalStore;
    }


//    private function getUniqueProductId($productId, $complementaryId = null)
//    {
//        $o = X::get("Ekom_productIdToUniqueProductId");
//        /**
//         * @var $o ProductIdToUniqueProductIdAdaptor
//         */
//        return $o->getUniqueProductId($productId, $complementaryId);
//    }

    private function sanitizeExtraArgs(array &$extraArgs)
    {
        $allowedExtraArgs = [];
        Hooks::call("Ekom_feedCartAllowedExtraArgs", $allowedExtraArgs);
        foreach ($extraArgs as $k => $v) {
            if (false === array_key_exists($k, $allowedExtraArgs)) {
                unset($extraArgs, $k);
            }
        }
    }


}
