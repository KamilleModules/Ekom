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
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Api\Util\HashUtil;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Utils\CartLocalStore;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomLinkHelper;
use QuickPdo\QuickPdo;


/**
 *
 * cart item
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
        $details = array_key_exists('details', $extraArgs) ? $extraArgs['details'] : null;
        $bundle = array_key_exists('bundle', $extraArgs) ? $extraArgs['bundle'] : null;

        if (is_array($details)) {
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
                    if ($details) {
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


            if (count($details) > 0) {
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
            if ((string)$item['id'] === $token) {
                $existingQty = $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'];
                $details = $this->getProductDetailsByToken($token);
                self::checkQuantityOverflow($productId, $existingQty, $newQty, $details, true);

                $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'] = $newQty;
                $wasUpdated = true;
                break;
            }
        }


        $this->writeToLocalStore();
        return (true === $wasUpdated);
    }


    /**
     * @param array $bag , array of coupon ids
     * @return $this
     */
//    public function setCouponBag(array $bag)
//    {
//        $this->initSessionCart();
//        $shopId = ApplicationRegistry::get("ekom.shop_id");
//        $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'] = $bag;
//        return $this;
//    }

    public function addCoupon($code)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");

        $couponInfo = CouponLayer::getCouponInfoByCode($code);
        if (false !== $couponInfo) {
            $currentBag = $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'];
            $ball = [
                "overrideEkom" => false,
                "coupons" => $currentBag,
            ];
            Hooks::call("Ekom_Cart_handleAddCoupon", $ball, $couponInfo);
            if (false === $ball['overrideEkom']) {
                // the newest coupon replaces all the other ones; only one coupon in the cart at a time
                $currentBag = [$couponInfo['id']];
            } else {
                $currentBag = $ball['coupons'];
            }
            $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'] = $currentBag;
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
            $ret = self::getCartModelByItems($items);
            $this->_cartModel = $ret;
        }
        return $this->_cartModel;
    }

    public function getExtendedCartModel()
    {
        $this->initSessionCart();
        $shopId = E::getShopId();
        $cartModel = $this->getCartModel();
        $coupons = $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'];
        throw new \Exception("todo");
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
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            if (null !== ($userId = SessionUser::getValue('id'))) {
                $this->getCartLocalStore()->saveUserCart($userId, $shopId, $_SESSION['ekom'][$this->sessionName][$shopId]);
            } else {
                XLog::error("[$this->moduleName] - $this->className: in shop#$shopId, this user doesn't have an id: " . ArrayToStringTool::toPhpArray($_SESSION));
            }
        }
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


    private static function getCartModelByItems(array $items, array $couponBag = [])
    {
        $model = [];
        $modelItems = [];
        $totalQty = 0;
        $totalWeight = 0;
        $linesTotal = 0;
        $taxAmountTotal = 0;


        //--------------------------------------------
        // CALCULATING LINE PRICES AND TOTAL
        //--------------------------------------------
        foreach ($items as $item) {

            $cartQuantity = $item['quantity'];
            $cartToken = $item['token'];
            $details = (array_key_exists('details', $item)) ? $item['details'] : [];
            $productDetails = CartUtil::getMergedProductDetails($details);
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
                $uriDetails = UriTool::uri($boxModel['uri_card_with_ref'], $productDetails, true);
                $linePrice = E::trimPrice($cartQuantity * $boxModel['priceSaleRaw']);
                $linesTotal += $linePrice;
                $itemTaxAmount = E::trimPrice($cartQuantity * $boxModel['taxAmountUnit']);
                $taxAmountTotal += $itemTaxAmount;

                $boxModel['cartToken'] = $cartToken;
                $boxModel['quantityCart'] = $cartQuantity;
                $boxModel['uri_card_with_details'] = $uriDetails;
                $boxModel['priceLineRaw'] = $linePrice;
                $boxModel['priceLine'] = E::price($linePrice);
                $boxModel['taxAmount'] = $itemTaxAmount;


                ksort($boxModel);
                $modelItems[] = $boxModel;
            } else {
                $className = self::getClassShortName();
                XLog::error("[Ekom module] - $className.getCartModelByItems: errorCode: " . $boxModel['errorCode'] . ", msg: " . $boxModel['errorMessage']);
            }

        }


        //--------------------------------------------
        // injecting cart level properties
        //--------------------------------------------
        $totalWeight = round($totalWeight, 2);
        $model['items'] = $modelItems;

        $model['totalCartQuantity'] = $totalQty;
        $model['totalCartWeight'] = $totalWeight;
        $model['totalTaxAmountRaw'] = $taxAmountTotal;
        $model['priceLinesTotalRaw'] = $linesTotal;


        //--------------------------------------------
        // CART TOTAL
        //--------------------------------------------
        $couponInfoItems = CouponLayer::getCouponInfoItemsByIds($couponBag);
        $couponsDetails = [];


        $cartTotal = self::applyCoupon("linesTotal", $model, $couponInfoItems, $couponsDetails);
        $model['priceCartTotalRaw'] = $cartTotal;



        $model['couponDetails'] = $couponsDetails;
        $model['couponHasCoupons'] = (count($couponsDetails) > 0);
        $model['couponSavingRaw'] = 0;




        //--------------------------------------------
        // SHIPPING
        //--------------------------------------------
        $carrierGroups = EkomApi::inst()->carrierLayer()->estimateShippingCosts($modelItems);


        //--------------------------------------------
        // MODULES
        //--------------------------------------------
        Hooks::call("Ekom_CartLayer_decorate_mini_cart_model", $model);


        //--------------------------------------------
        // ROUND UP
        //--------------------------------------------
        /**
         * Note: this allows modules to deal only with raw values (in case they change the cart model)
         */
        $model['totalTaxAmount'] = E::price($model['totalTaxAmountRaw']);
        $model['priceLinesTotal'] = E::price($model['priceLinesTotalRaw']);
        $model['priceCartTotal'] = E::price($model['priceCartTotalRaw']);

        return $model;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
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
     * @throws EkomUserMessageException
     */
    private static function checkQuantityOverflow($productId, $existingQty, $qty, array $details, $isUpdate = false)
    {
        if (false === E::conf('acceptOutOfStockOrders', false)) {

            $productDetailsArgs = CartUtil::getMergedProductDetails($details);
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

    private function isConfigurableProduct($productId, array $details)
    {
        return (
            array_key_exists('minor', $details) &&
            count($details['minor']) > 0 // assuming it's an array already..
        );
    }

    private static function getClassShortName()
    {
        $s = get_called_class();
        $p = explode('\\', $s);
        return array_pop($p);
    }
}
