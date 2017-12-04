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
class CartLayerOld3
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
        $shopId = E::getShopId();

        /**
         * The product details array (not productDetailsArgs)
         */
        $details = array_key_exists('details', $extraArgs) ? $extraArgs['details'] : [];
        $bundle = array_key_exists('bundle', $extraArgs) ? $extraArgs['bundle'] : null;

        $isValidDetails = (array_key_exists('major', $details) && array_key_exists('minor', $details));


        if ($isValidDetails) {
            $majorDetailsParams = $details['major'];
        } else {
            $majorDetailsParams = [];
        }


        $token = CartUtil::generateTokenByProductIdMajorProductDetails($productId, $majorDetailsParams);
        $alreadyExists = false;
        $remainingStockQty = null;
        //--------------------------------------------
        // UPDATE MODE
        //--------------------------------------------
        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
            if ($item['token'] === $token) {

                $isConfigurable = $this->isConfigurableProduct($productId, $details);
                if (false === $isConfigurable) {

                    $existingQuantity = $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'];
                    self::checkQuantityOverflow($productId, $existingQuantity, $quantity, $details);

                    $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'] += $quantity;
                    if ($isValidDetails) {
                        $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['details'] = $details;
                    }
                    $alreadyExists = true;
                    break;
                } else {
                    self::checkQuantityOverflow($productId, 0, $quantity, $details);
                    $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'] = $quantity;
                    $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['details'] = $details;
                    $alreadyExists = true;
                    break;
                }
            }
        }


        //--------------------------------------------
        // INSERT MODE
        //--------------------------------------------
        if (false === $alreadyExists) {

            self::checkQuantityOverflow($productId, 0, $quantity, $details);

            $arr = [
                "quantity" => $quantity,
                "token" => $token,
                "product_id" => $productId,
            ];


            if ($isValidDetails) {
                $arr['details'] = $details;
            }

            if (null !== $bundle) {
                $arr['bundle'] = (int)$bundle;
            }

            // adding other args
            self::decorateWithExtraArgs($arr, $extraArgs);
            $_SESSION['ekom'][$this->sessionName][$shopId]['items'][] = $arr;
        }

        // modules might have change the session even if this method didn't add the item.
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
        $shopId = E::getShopId();
        $token = (string)$token;
        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
            if ($item['token'] === $token) {
                unset($_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]);
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
        $shopId = E::getShopId();
        $productId = self::getProductIdByCartToken($token);


        $newQty = (int)$newQty;
        if ($newQty < 0) {
            $newQty = 0;
        }

        $wasUpdated = false;
        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
            if ($item['token'] === $token) {
                $existingQty = $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'];
                $details = $this->getProductDetailsByToken($token);
                self::checkQuantityOverflow($productId, $existingQty, $newQty, $details, true);

                $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'] = $newQty;
                $wasUpdated = true;
                break;
            }
        }


        $this->writeToLocalStore();
//        az($_SESSION['ekom'][$this->sessionName]);
        return (true === $wasUpdated);
    }


    public function addCoupon($code)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");

        $couponInfo = CouponLayer::getCouponInfoByCode($code);
        if (false !== $couponInfo) {
            $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'] = [$couponInfo['id']];
            $this->writeToLocalStore();
            return true;
        }
        return false;
    }

    public function removeCoupon($code)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");

        $couponInfo = CouponLayer::getCouponInfoByCode($code);
        if (false !== $couponInfo) {
            $index = array_search($couponInfo['id'], $_SESSION['ekom'][$this->sessionName][$shopId]['coupons']);
            unset($_SESSION['ekom'][$this->sessionName][$shopId]['coupons'][$index]);
            $this->writeToLocalStore();
        }
        return $this;
    }


    public function setCartContent(array $cart, $shopId = null)
    {
        $this->initSessionCart();
        $shopId = E::getShopId($shopId);
        $_SESSION['ekom'][$this->sessionName][$shopId] = $cart;
        $this->writeToLocalStore();
    }

    public function clean()
    {
        $_SESSION['ekom'][$this->sessionName] = [];
        $this->writeToLocalStore();
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public function getCartModel()
    {
        if (null === $this->_cartModel) {
            $this->initSessionCart();
            $shopId = E::getShopId();
            $items = $_SESSION['ekom'][$this->sessionName][$shopId]['items'];
            $coupons = $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'];
            $ret = self::deGetCartModel($items, $coupons);
            $this->_cartModel = $ret;
        }
        return $this->_cartModel;
    }


    public function getItems()
    {
        $this->initSessionCart();
        $shopId = E::getShopId();
        return $_SESSION['ekom'][$this->sessionName][$shopId]['items'];
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


    public function getCartContent($shopId = null)
    {
        $this->initSessionCart();
        $shopId = E::getShopId($shopId);
        return $_SESSION['ekom'][$this->sessionName][$shopId];
    }

    public function getCouponBag()
    {
        $this->initSessionCart();
        $shopId = E::getShopId();
        return $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'];
    }


    public function getContext()
    {
        $this->initSessionCart();
        $shopId = E::getShopId();
        return $_SESSION['ekom'][$this->sessionName][$shopId];
    }


    public function getCartItemByToken($token)
    {
        $this->initSessionCart();
        $token = (string)$token;
        $shopId = E::getShopId();

        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
            if ($item['token'] === $token) {
                return $item;
            }
        }
        return false;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function writeToLocalStore()
    {
        if (true === SessionUser::isConnected()) {
            $shopId = E::getShopId();
            if (null !== ($userId = SessionUser::getValue('id'))) {
                if (array_key_exists($shopId, $_SESSION['ekom'][$this->sessionName])) {
                    $this->getCartLocalStore()->saveUserCart($userId, $shopId, $_SESSION['ekom'][$this->sessionName][$shopId]);
                }
            } else {
                XLog::error("[$this->moduleName] - $this->className: in shop#$shopId, this user doesn't have an id: " . ArrayToStringTool::toPhpArray($_SESSION));
            }
        }
        /**
         * A change in the cart should invalidate the cache
         */
        $this->_cartModel = null;
    }

    protected function initSessionCart()
    {
        $shopId = E::getShopId();
        if (
            false === array_key_exists('ekom', $_SESSION) ||
            false === array_key_exists($this->sessionName, $_SESSION['ekom'])
        ) {
            $_SESSION['ekom'][$this->sessionName] = [];
        }
        if (false === array_key_exists($shopId, $_SESSION['ekom'][$this->sessionName])) {
            $defaultCart = [
                'items' => [],
                'coupons' => [],
            ];
            $_SESSION['ekom'][$this->sessionName][$shopId] = $defaultCart;
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
     * @param array $couponBag
     * @return array:cartModel
     * @see EkomModels::cartModel()
     *
     */
    private static function deGetCartModel(array $items, array $couponBag = [])
    {

        /**
         * @todo-ling: lang should be taken from the gcp I believe, since
         * gpc is what drives the display of products inside the cart.
         * Often, it should be the same as E::getLangId (which I use temporarily below),
         * but in the long term I believe langId should be taken from gpc directly.
         */
        $langId = E::getLangId();
        $shopId = E::getShopId();


        $model = [];
        $modelItems = [];


        $totalQty = 0;
        $totalWeight = 0;
        $cartTotal = 0;
        $cartTotalWithoutTax = 0;
        $cartTaxAmount = 0;


        //--------------------------------------------
        // BOXES
        //--------------------------------------------
        foreach ($items as $item) {

            $cartQuantity = $item['quantity'];
            $cartToken = $item['token'];
            $details = (array_key_exists('details', $item)) ? $item['details'] : [];
            $productDetails = ProductBoxEntityUtil::getMergedProductDetails($details);
            $productId = self::getProductIdByCartToken($cartToken);


            $boxModel = ProductBoxLayer::getProductBoxByProductId($productId, $productDetails);
            if (false === array_key_exists('errorCode', $boxModel)) {


                //--------------------------------------------
                // updating total weight and quantities
                //--------------------------------------------
                $weight = $boxModel['weight'];
                $totalQty += $cartQuantity;
                $totalWeight += $weight * $cartQuantity;


                //--------------------------------------------
                // extending the box model to
                //--------------------------------------------
                $uriDetails = UriTool::uri($boxModel['uriProduct'], $productDetails, true);


                $linePrice = E::trimPrice($cartQuantity * $boxModel['priceSaleRaw']);
                $cartTotal += $linePrice;

                $linePriceWithoutTax = E::trimPrice($cartQuantity * $boxModel['priceBaseRaw']);
                $cartTotalWithoutTax += $linePriceWithoutTax;

                $itemTaxAmount = E::trimPrice($cartQuantity * $boxModel['taxAmount']);
                $cartTaxAmount += $itemTaxAmount;


                $boxModel['cartToken'] = $cartToken;
                $boxModel['quantityCart'] = $cartQuantity;
                $boxModel['uri_card_with_details'] = $uriDetails;
                $boxModel['priceLineRaw'] = $linePrice;
                $boxModel['priceLine'] = E::price($linePrice);
                $boxModel['priceLineWithoutTaxRaw'] = $linePriceWithoutTax;
                $boxModel['priceLineWithoutTax'] = E::price($linePriceWithoutTax);
                $boxModel['taxAmount'] = $itemTaxAmount;


                ksort($boxModel);
                $modelItems[] = $boxModel;
            } else {
                $className = self::getClassShortName();
                XLog::error("[Ekom module] - $className.deGetCartModel: errorCode: " . $boxModel['errorCode'] . ", msg: " . $boxModel['errorMessage']);
            }

        }

//        az($modelItems);

        //--------------------------------------------
        // CART
        //--------------------------------------------
        $totalWeight = round($totalWeight, 2);
        $model['items'] = $modelItems;
        $model['cartTotalQuantity'] = $totalQty;
        $model['cartTotalWeight'] = $totalWeight;
        $model['cartTaxAmountRaw'] = $cartTaxAmount;
        $model['priceCartTotalRaw'] = $cartTotal;
        $model['priceCartTotalWithoutTaxRaw'] = $cartTotal - $cartTaxAmount;


        //--------------------------------------------
        // ORDER
        //--------------------------------------------

        //--------------------------------------------
        // SHIPPING
        //--------------------------------------------

        /**
         * @see https://github.com/KamilleModules/Ekom/tree/master/doc/cart/cart-shipping-cost-algorithm.md
         */
        $shippingInfo = false;
        $shopAddress = null;
        $carrier = null;
        if ($totalWeight > 0) {
            /**
             * Is there a carrier available?
             */
            $carrier = self::getCheckoutCarrier($shopId);


            /**
             * Can the carrier calculate the shippingInfo with the given context?
             */
            $context = CartUtil::getCarrierShippingInfoContext($model);
            $shippingInfo = $carrier->getShippingInfo($context);
        }


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
            $model["shippingTaxAmountUnit"] = $taxInfo['taxAmountUnit'];
            $model["shippingTaxHasTax"] = ($taxInfo['taxAmountUnit'] > 0); // whether or not the tax was applied
            $model["shippingDetails"] = [
                "estimated_delivery_text" => $shippingInfo["estimated_delivery_text"],
                "estimated_delivery_date" => $shippingInfo["estimated_delivery_date"],
                "label" => $carrier->getLabel(),
//                "shop_address" => $shopAddress, // not sure?
                "carrier_id" => $carrier->getId(),
            ];
            $model["shippingShippingCostRaw"] = $shippingCostWithTax;
            $model["shippingIsApplied"] = true;
            $model['shippingErrorCode'] = null;
        } else {
            $shippingCostWithTax = 0;
            $model["shippingTaxDetails"] = [];
            $model["shippingTaxRatio"] = 1;
            $model["shippingTaxGroupName"] = "";
            $model["shippingTaxGroupLabel"] = "";
            $model["shippingTaxAmountUnit"] = 0;
            $model["shippingTaxHasTax"] = false;
            $model["shippingDetails"] = [];
            $model["shippingShippingCostRaw"] = $shippingCostWithTax;
            $model["shippingIsApplied"] = false;

            if (is_array($shippingInfo) && array_key_exists("errorCode", $shippingInfo)) {
                $model['shippingErrorCode'] = $shippingInfo['errorCode'];
            } else {
                $model['shippingErrorCode'] = null;
            }
        }




        // order total
        $orderTotal = $cartTotal + $shippingCostWithTax;
        $model["priceOrderTotalRaw"] = $orderTotal;


        //--------------------------------------------
        // coupons
        //--------------------------------------------
//        az(__FILE__, $couponBag);
        $couponInfoItems = CouponLayer::getCouponInfoItemsByIds($couponBag, $langId);
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
        $model['priceOrderGrandTotalRaw'] = $orderGrandTotal;


        //--------------------------------------------
        // COMBINING ALL COUPONS
        //--------------------------------------------
        $model['couponDetails'] = $couponsDetails;
        $model['couponHasCoupons'] = (count($couponsDetails) > 0);
        $model['couponSavingRaw'] = $orderTotal - $orderGrandTotal;

        //--------------------------------------------
        // MODULES
        //--------------------------------------------
        Hooks::call("Ekom_CartLayer_decorateCartModel", $model);


        //--------------------------------------------
        // ROUND UP
        //--------------------------------------------
        /**
         * Note: this allows modules to deal only with raw values (in case they change the cart model)
         */
        $model['cartTaxAmount'] = E::price($model['cartTaxAmountRaw']);
        $model['priceCartTotal'] = E::price($model['priceCartTotalRaw']);
        $model['priceCartTotalWithoutTax'] = E::price($model['priceCartTotalWithoutTaxRaw']);
        $model['couponSaving'] = E::price($model['couponSavingRaw']);

        // order
        $model["shippingShippingCost"] = E::price($model["shippingShippingCostRaw"]);
        $model["priceOrderTotal"] = E::price($model["priceOrderTotalRaw"]);
        $model["priceOrderGrandTotal"] = E::price($model["priceOrderGrandTotalRaw"]);


        // older
//        $model['totalTaxItemsAmount'] = E::price($model['totalTaxItemsAmountRaw']);
//        $model['totalTaxCartAmount'] = E::price($model['totalTaxCartAmountRaw']);
//        $model['priceLinesTotal'] = E::price($model['priceLinesTotalRaw']);
//        $model['priceLinesTotalWithoutTax'] = E::price($model['priceLinesTotalWithoutTaxRaw']);
//        $model['priceCartTotal'] = E::price($model['priceCartTotalRaw']);
//        $model['priceCartTotalWithShipping'] = E::price($model['priceCartTotalWithShippingRaw']);
//        $model['priceGrandTotal'] = E::price($model['priceGrandTotalRaw']);

        ksort($model);
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
        if (false === E::conf('acceptOutOfStockOrders', false)) {

            $productDetailsArgs = ProductBoxEntityUtil::getMergedProductDetails($details);

            $boxModel = ProductBoxLayer::getProductBoxByProductId($productId, $productDetailsArgs);


            $remainingStockQty = $boxModel['quantityStock'];

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
        $details = [
            'major' => [],
            'minor' => [],
        ];
        if (false !== ($item = $this->getCartItemByToken($token))) {
            if (array_key_exists('details', $item)) {
                $details = $item['details'];
            }
        }
        return $details;
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

    private static function getCheckoutCarrier($shopId)
    {
        $carrierId = CurrentCheckoutData::getCarrierId();
        if (null !== $carrierId) {
            return CarrierLayer::getCarrierInstanceById($carrierId, $shopId);
        }
        $carrierId = CarrierLayer::getShopDefaultCarrierId($shopId);
        return CarrierLayer::getCarrierInstanceById($carrierId, $shopId);
    }


}
