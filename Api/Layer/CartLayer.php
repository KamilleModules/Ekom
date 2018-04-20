<?php


namespace Module\Ekom\Api\Layer;


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Bat\ClassTool;
use Bat\UriTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\ProductBoxEntityUtil;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Api\Util\HashUtil;
use Module\Ekom\Carrier\CarrierInterface;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Utils\CartLocalStore;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomLinkHelper;
use QuickPdo\QuickPdo;


/**
 *
 * sessionCartItem
 * ====================
 *
 * A typical item looks like this:
 *
 * - token: string, the token computed by this class from the product_id and the major details if any.
 *              The user cannot override it.
 * - product_id: int
 * - quantity: int
 * - ?details
 *      - major
 *      - minor
 *
 * - ?bundle: int, the bundle id
 * - ...
 *
 *
 *
 *
 */
class CartLayer
{


    /**
     * @var CartLocalStore
     */
    protected $cartLocalStore;
    protected $sessionName;

    /**
     * used in debug/error messages
     */
    protected $className;
    protected $moduleName;

    private $_cartModel; // cache


    public function __construct()
    {
        $this->_cartModel = null;
        $this->sessionName = 'cart';
        $this->className = 'CartLayer';
        $this->moduleName = 'Ekom';

    }


    public static function create()
    {
        return new static();
    }

    /**
     *
     * @throws EkomUserMessageException when something wrong that the user should know happens
     *
     */
    public function addItem($quantity, $productId, array $extraArgs = [])
    {
        $this->initSessionCart();

        /**
         * The selected product details array
         */
        $selectedProductDetails = array_key_exists('details', $extraArgs) ? $extraArgs['details'] : [];
        $bundle = array_key_exists('bundle', $extraArgs) ? $extraArgs['bundle'] : null;


        $token = CartUtil::generateTokenByProductIdMajorProductDetails($productId, $selectedProductDetails);


        $alreadyExists = false;
        $remainingStockQty = null;
        //--------------------------------------------
        // UPDATE MODE, adding to the existing quantity
        //--------------------------------------------
        // note to myself: I'm not sure why we don't use token as the key, maybe it's about items order?
        foreach ($_SESSION['ekom'][$this->sessionName]['items'] as $k => $item) {

            if ($token === $item['token']) {

                $alreadyExists = true;
                $existingQuantity = $_SESSION['ekom'][$this->sessionName]['items'][$k]['quantity'];
                self::checkQuantityOverflow($productId, $existingQuantity, $quantity, $selectedProductDetails);
                $_SESSION['ekom'][$this->sessionName]['items'][$k]['quantity'] += $quantity;
                break;
            }
        }


        //--------------------------------------------
        // INSERT MODE, adding a new quantity
        //--------------------------------------------
        if (false === $alreadyExists) {

            self::checkQuantityOverflow($productId, 0, $quantity, $selectedProductDetails);
            $cartItemBox = CartItemBoxLayer::getBox($productId, $selectedProductDetails);
//            az(__FILE__, $cartItemBox);
            $arr = [
                "token" => $token,
                "quantity" => $quantity,
                "box" => $cartItemBox,
            ];


            if (null !== $bundle) {
                $arr['bundle'] = (int)$bundle;
            }

            // adding other args
            self::decorateWithExtraArgs($arr, $extraArgs);
            $_SESSION['ekom'][$this->sessionName]['items'][] = $arr;
        }


        $this->writeToLocalStore();
    }


    /**
     * This is used by the bundle system, or by developers manually.
     * @param array $cartItems , array of cartItems as defined at the top of this class.
     *                  The following are required:
     *                  - product_id
     * @throws EkomApiException when something wrong occurs
     */
    public function addItems(array $cartItems)
    {
        foreach ($cartItems as $cartItem) {

            $productId = null;
            if (array_key_exists('product_id', $cartItem)) {
                $productId = (int)$cartItem['product_id'];
                $quantity = (array_key_exists('quantity', $cartItem)) ? (int)$cartItem['quantity'] : 1;
                unset($cartItem['product_id']);
                unset($cartItem['quantity']);
                $this->addItem($quantity, $productId, $cartItem);
            } else {
                throw new EkomApiException("invalid cart item, product_id is missing");
            }
        }
        $this->writeToLocalStore();
    }


    public function removeItem($token)
    {
        $this->initSessionCart();
        $token = (string)$token;
        foreach ($_SESSION['ekom'][$this->sessionName]['items'] as $k => $item) {
            if ($item['token'] === $token) {
                unset($_SESSION['ekom'][$this->sessionName]['items'][$k]);
                break;
            }
        }
        $this->writeToLocalStore();
    }


    /**
     *
     * Set the quantity for a given product in the cart,
     * with respect of the acceptOutOfStockOrders directive,
     * and return whether or not the cart was actually updated.
     *
     *
     * @return false|true
     *          if false, a problem occurred, you can get the error with the errors array.
     *          if true, it means the quantity has been added to the cart.
     *
     * @throws EkomUserMessageException when an error message needs to be delivered to the front customer
     */
    public function updateItemQuantity($token, $newQty)
    {
        $this->initSessionCart();
        $token = (string)$token;
        $productId = self::getProductIdByCartToken($token);


        $newQty = (int)$newQty;
        if ($newQty < 0) {
            $newQty = 0;
        }

        $wasUpdated = false;
        foreach ($_SESSION['ekom'][$this->sessionName]['items'] as $k => $item) {
            if ($item['token'] === $token) {
                $existingQty = $_SESSION['ekom'][$this->sessionName]['items'][$k]['quantity'];
                $details = $this->getProductDetailsByToken($token);
                self::checkQuantityOverflow($productId, $existingQty, $newQty, $details, true);

                $_SESSION['ekom'][$this->sessionName]['items'][$k]['quantity'] = $newQty;
                $wasUpdated = true;
                break;
            }
        }


        $this->writeToLocalStore();
//        az($_SESSION['ekom'][$this->sessionName]);
        return (true === $wasUpdated);
    }


    public function addCoupon($couponId)
    {
        $this->initSessionCart();
        $_SESSION['ekom'][$this->sessionName]['coupons'] = [$couponId];
        $this->writeToLocalStore();
    }

    public function removeCoupon($code)
    {
        $this->initSessionCart();

        $couponInfo = CouponLayer::getCouponInfoByCode($code);
        if (false !== $couponInfo) {
            $index = array_search($couponInfo['id'], $_SESSION['ekom'][$this->sessionName]['coupons']);
            unset($_SESSION['ekom'][$this->sessionName]['coupons'][$index]);
            $this->writeToLocalStore();
        }
        return $this;
    }


    public function setCartContent(array $cart)
    {
        $this->initSessionCart();
        $_SESSION['ekom'][$this->sessionName] = $cart;
        $this->writeToLocalStore();
    }

    public function clean($source = null)
    {
        /**
         * Source is being set to "checkout" by CheckoutOrderUtil
         * when it cleans the cart after placing the order
         */
        $_SESSION['ekom'][$this->sessionName] = [
            "items" => [],
            'coupons' => [],
        ];
        $this->writeToLocalStore($source);
        return $this;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public function getCartModel()
    {
        if (null === $this->_cartModel) {
            $this->initSessionCart();
            $items = $_SESSION['ekom'][$this->sessionName]['items'];
            $coupons = $_SESSION['ekom'][$this->sessionName]['coupons'];
            $ret = self::doGetCartModel($items, $coupons);
            $this->_cartModel = $ret;
        }
        return $this->_cartModel;
    }


    /**
     *
     * This method is useful if you have stored the cart in the database, and now you want to redisplay it.
     *
     * @param array $items
     * @param array $coupons
     * @return array
     */
    public static function getCartModelByItemsAndCoupons(array $items, array $coupons = [])
    {
        return self::doGetCartModel($items, $coupons);
    }


    public function getItems()
    {
        $this->initSessionCart();
        return $_SESSION['ekom'][$this->sessionName]['items'];
    }


    public function getQuantity($token)
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            if ($item['token'] === $token) {
                return $item['quantity'];
            }
        }
        return 0;
    }


    public function getCartContent()
    {
        $this->initSessionCart();
        return $_SESSION['ekom'][$this->sessionName];
    }

    public function getCouponBag()
    {
        $this->initSessionCart();
        return $_SESSION['ekom'][$this->sessionName]['coupons'];
    }


    public function getContext()
    {
        $this->initSessionCart();
        return $_SESSION['ekom'][$this->sessionName];
    }


    public function getCartItemByToken($token)
    {
        $this->initSessionCart();
        $token = (string)$token;

        foreach ($_SESSION['ekom'][$this->sessionName]['items'] as $k => $item) {
            if ($item['token'] === $token) {
                return $item;
            }
        }
        return false;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function writeToLocalStore($operationName = null)
    {
        $cart = $_SESSION['ekom'][$this->sessionName];
        Hooks::call("Ekom_onCartUpdate", $userId, $cart, $operationName);
        /**
         * A change in the cart should invalidate the cache
         */
        $this->_cartModel = null;
    }

    protected function initSessionCart()
    {

        if (
            false === array_key_exists('ekom', $_SESSION) ||
            false === array_key_exists($this->sessionName, $_SESSION['ekom'])
        ) {
            $_SESSION['ekom'][$this->sessionName] = [
                'items' => [],
                'coupons' => [],
            ];
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function decorateWithExtraArgs(array &$arr, array $extraArgs)
    {
        unset($extraArgs['token']);
        unset($extraArgs['product_id']);
        unset($extraArgs['quantity']);
        unset($extraArgs['details']);
        unset($extraArgs['bundle']);
        if ($extraArgs) {
            foreach ($extraArgs as $k => $v) {
                $arr[$k] = $v;
            }
        }
    }

    private static function getProductIdByCartToken($token)
    {
        return explode('-', $token)[0];
    }


    /**
     * @param array $items
     * @param array $coupons
     * @return array:cartModel
     * @see EkomModels::cartModel()
     *
     */
    private static function doGetCartModel(array $items, array $coupons = [])
    {

        $model = [];
        $modelItems = [];


        $cartTotalWeight = 0;
        $cart_total_quantity = 0;
        $cartTotalTaxIncluded = 0;
        $cartDiscountAmount = 0;
        $cartTaxAmount = 0;
        $cartTaxDistribution = [];


        //--------------------------------------------
        // BOXES
        //--------------------------------------------
        foreach ($items as $item) {

            $cartQuantity = $item['quantity'];
            $cartToken = $item['token'];
            $boxModel = $item['box'];


            //--------------------------------------------
            // updating total weight and quantities
            //--------------------------------------------
            $weight = $boxModel['weight'];
            $cart_total_quantity += $cartQuantity;
            $cartTotalWeight += $weight * $cartQuantity;


            $lineRealPrice = E::trimPrice($cartQuantity * $boxModel['real_price']);
            $lineBasePrice = E::trimPrice($cartQuantity * $boxModel['base_price']);
            $lineSalePrice = E::trimPrice($cartQuantity * $boxModel['sale_price']);


            $unitTaxAmount = $boxModel['sale_price'] - $boxModel['base_price'];
            $lineTaxAmount = E::trimPrice($cartQuantity * $unitTaxAmount);
            $cartTaxAmount += $lineTaxAmount;


            $cartTotalTaxIncluded += $cartQuantity * $boxModel['sale_price'];


            if (true === $boxModel['has_discount']) {
                $discountAmount = E::trimPrice($boxModel['base_price'] - $boxModel['real_price']);
                $cartDiscountAmount += $discountAmount;
            }


            $lineTaxDetails = [];
            foreach($boxModel['tax_details'] as $taxDetail){
                $lineTaxDetails[$taxDetail['label']] = $cartQuantity * $taxDetail['amount'];
            }

            TaxLayer::decorateTaxDistribution($cartTaxDistribution, $boxModel['base_price'], $boxModel['tax_details']);


            $boxModel['cart_quantity'] = $cartQuantity;
            $boxModel['cart_token'] = $cartToken;
            $boxModel['line_real_price'] = $lineRealPrice;
            $boxModel['line_real_price_formatted'] = E::price($lineRealPrice);
            $boxModel['line_base_price'] = $lineBasePrice;
            $boxModel['line_base_price_formatted'] = E::price($lineBasePrice);
            $boxModel['line_sale_price'] = $lineSalePrice;
            $boxModel['line_sale_price_formatted'] = E::price($lineSalePrice);
            $boxModel['line_tax_details'] = $lineTaxDetails;

            $modelItems[] = $boxModel;

        }

//        az($modelItems);

        //--------------------------------------------
        // CART
        //--------------------------------------------
        $cartTotalWeight = round($cartTotalWeight, 2);
        $model['items'] = $modelItems;
        $model['cart_total_weight'] = $cartTotalWeight;
        $model['cart_total_quantity'] = $cart_total_quantity;
        $model['cart_total_tax_excluded'] = $cartTotalTaxIncluded - $cartTaxAmount;
        $model['cart_total_tax_excluded_formatted'] = E::price($model['cart_total_tax_excluded']);
        $model['cart_total_tax_included'] = $cartTotalTaxIncluded;
        $model['cart_total_tax_included_formatted'] = E::price($cartTotalTaxIncluded);
        $model['cart_discount_amount'] = $cartDiscountAmount;
        $model['cart_discount_amount_formatted'] = E::price($cartDiscountAmount);
        $model['cart_tax_amount'] = $cartTaxAmount;
        $model['cart_tax_amount_formatted'] = E::price($cartTaxAmount);
        $model['cart_tax_distribution'] = $cartTaxDistribution;

        //--------------------------------------------
        // ORDER
        //--------------------------------------------

        //--------------------------------------------
        // SHIPPING
        //--------------------------------------------

        $carrierId = null;
        $carrierLabel = "";
        $carrierEstimatedDeliveryDate = "";
        $carrierHasError = false;
        $carrierErrorCode = null;

        $shippingCostTaxExcluded = 0;
        $shippingCostTaxIncluded = 0;
        $shippingCostDiscountAmount = 0;
        $shippingCostTaxAmount = 0;
        $shippingCostTaxLabel = "";


        $shippingInfo = false;
        $shopAddress = null;
        $carrier = null;


        /**
         * @see https://github.com/KamilleModules/Ekom/tree/master/doc/cart/cart-shipping-cost-algorithm.md
         */
        if ($cartTotalWeight > 0) {
            /**
             * Is there a carrier available?
             */
            $carrier = self::chooseCarrier();

            /**
             * Can the carrier calculate the shippingInfo with the given context?
             */
            $context = CartUtil::getCarrierShippingInfoContext($model);
            $shippingInfo = $carrier->getShippingInfo($context);


            if (true === CartUtil::isValidShippingInfo($shippingInfo)) {


                // applying shipping taxes
                //--------------------------------------------
                $taxInfo = CartUtil::getTaxInfoByValidShippingInfo($shippingInfo, $model);

                $shippingCostWithTax = E::trimPrice($taxInfo['priceWithTax']);
                $shippingCost = $taxInfo['priceWithoutTax'];

                $model["shippingTaxDetails"] = $taxInfo['taxDetails'];
                $model["shippingTaxRatio"] = $taxInfo['taxRatio'];
                $model["shippingTaxGroupName"] = $taxInfo['taxGroupName'];
                $model["shippingTaxGroupLabel"] = $taxInfo['taxGroupLabel'];
                $model["shippingTaxAmountRaw"] = $taxInfo['taxAmountUnit'];
                $model["shippingTaxHasTax"] = ($taxInfo['taxAmountUnit'] > 0); // whether or not the tax was applied
                $model["shippingDetails"] = [
                    "estimated_delivery_text" => $shippingInfo["estimated_delivery_text"],
                    "estimated_delivery_date" => $shippingInfo["estimated_delivery_date"],
                    "label" => $carrier->getLabel(),
//                "shop_address" => $shopAddress, // not sure?
                    "carrier_id" => $carrier->getId(),
                ];
                $model["shippingShippingCostRaw"] = $shippingCostWithTax;
                $model["shippingShippingCostWithoutTaxRaw"] = $shippingCost;
                $model["shippingIsApplied"] = true;
                $model['shippingErrorCode'] = null;
            }
        }


        $model['carrier_id'] = $carrierId;
        $model['carrier_label'] = $carrierLabel;
        $model['carrier_estimated_delivery_date'] = $carrierEstimatedDeliveryDate;
        $model['carrier_has_error'] = $carrierHasError;
        $model['carrier_error_code'] = $carrierErrorCode;
        $model['shipping_cost_tax_excluded'] = $shippingCostTaxExcluded;
        $model['shipping_cost_tax_excluded_formatted'] = E::price($shippingCostTaxExcluded);
        $model['shipping_cost_tax_included'] = $shippingCostTaxIncluded;
        $model['shipping_cost_tax_included_formatted'] = E::price($shippingCostTaxIncluded);
        $model['shipping_cost_discount_amount'] = $shippingCostDiscountAmount;
        $model['shipping_cost_discount_amount_formatted'] = E::price($shippingCostDiscountAmount);
        $model['shipping_cost_tax_amount'] = $shippingCostTaxAmount;
        $model['shipping_cost_tax_amount_formatted'] = E::price($shippingCostTaxAmount);
        $model['shipping_cost_tax_label'] = $shippingCostTaxLabel;


        //--------------------------------------------
        // COUPONS
        //--------------------------------------------
        $hasCoupons = (!empty($coupons));
        $couponsTotal = 0;
        $model['has_coupons'] = $hasCoupons;
        $model['coupons_total'] = $couponsTotal;
        $model['coupons_total_formatted'] = E::price($couponsTotal);
        $model['coupons'] = $coupons;


        if ('todo' === "deprecated?") {


            $couponInfoItems = CouponLayer::getCouponInfoItemsByIds($coupons);
            $couponsDetails = [];
            /**
             * @todo-ling, the coupons potentially can change ANYTHING in the model.
             * To handle this versatility, we use php Classes which will be able to do ANYTHING.
             *
             * However, we use an array as the model, which makes it painful for evolution: if the ekom cartModel
             * changes (and I bet it will), then all existing code needs to be re-written.
             * To mitigate this, I suggest a Helper class provided by Ekom with standard methods taking care of
             * doing the dirty work (for instance if you do a 2 bought, 1 free, the Helper will let you call a simple
             * method for that).
             *
             */
            $orderGrandTotal = CouponLayer::applyCoupons($couponInfoItems, $orderTotal, $model, $couponsDetails);
        }


        //--------------------------------------------
        // ORDER
        //--------------------------------------------
        $orderTotal = $model['cart_total_tax_included'] + $model['shipping_cost_tax_included'];
        $orderGrandTotal = $orderTotal - $couponsTotal;
        $orderTaxAmount = $model['cart_tax_amount'] + $model['shipping_cost_tax_amount'];
        $orderDiscountAmount = $model['cart_discount_amount'] + $model['shipping_cost_discount_amount'];
        $orderSavingTotal = $orderDiscountAmount + $couponsTotal;

        $model['order_total'] = $orderTotal;
        $model['order_total_formatted'] = E::price($orderTotal);
        $model['order_grand_total'] = $orderGrandTotal;
        $model['order_grand_total_formatted'] = E::price($orderGrandTotal);
        $model['order_tax_amount'] = $orderTaxAmount;
        $model['order_tax_amount_formatted'] = E::price($orderTaxAmount);
        $model['order_discount_amount'] = $orderDiscountAmount;
        $model['order_discount_amount_formatted'] = E::price($orderDiscountAmount);
        $model['order_saving_total'] = $orderSavingTotal;
        $model['order_saving_total_formatted'] = E::price($orderSavingTotal);


        return $model;
    }



    //--------------------------------------------
    //
    //--------------------------------------------

    /**
     *
     *
     * @param $productId
     * @param $existingQty
     * @param $qty ,
     *              if isUpdate=false, the addedQty
     *              if isUpdate=true, the newQty
     *
     * @param array $details , the product details array
     * @param bool $isUpdate
     * @throws \Exception
     */
    private static function checkQuantityOverflow($productId, $existingQty, $qty, array $details, $isUpdate = false)
    {
        return false;
        if (false === E::conf('acceptOutOfStockOrders', false)) {

            $productDetailsArgs = ProductBoxEntityUtil::getMergedProductDetails($details);

            $boxModel = ProductBoxLayer::getProductBoxByProductId($productId, $productDetailsArgs);


            $remainingStockQty = $boxModel['quantity'];

            if (false === $isUpdate) {
                $addedQty = $qty;
                $desiredQty = $existingQty + $addedQty;
                if (-1 !== $remainingStockQty && $desiredQty > $remainingStockQty) {
                    throw new EkomUserMessageException("Cannot add $addedQty products to the cart (only $remainingStockQty left in stock)");
                }
            } else {
                $newQty = $qty;
                if (-1 !== $remainingStockQty && $newQty > $remainingStockQty) {
                    throw new EkomUserMessageException("Cannot set $newQty products to the cart (only $remainingStockQty left in stock)");
                }
            }
        }
    }

    private static function getClassShortName()
    {
        $s = get_called_class();
        $p = explode('\\', $s);
        return array_pop($p);
    }


    private function getProductDetailsByToken($token)
    {
        if (false !== ($item = $this->getCartItemByToken($token))) {
            return $item['box']['selected_product_details'];
        }
        return [];
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


    private function isConfigurableProduct($productId, array $details)
    {
        return (
            array_key_exists('minor', $details) &&
            count($details['minor']) > 0 // assuming it's an array already..
        );
    }


    /**
     * @return CarrierInterface
     */
    private static function chooseCarrier()
    {
        $carrierId = CurrentCheckoutData::getCarrierId();
        if (null !== $carrierId) {
            return CarrierLayer::getCarrierInstanceById($carrierId);
        }
        $carrierId = CarrierLayer::getShopDefaultCarrierId();
        return CarrierLayer::getCarrierInstanceById($carrierId);
    }


}
