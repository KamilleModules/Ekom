<?php


namespace Module\Ekom\Api\Layer;


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Bat\UriTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Api\Util\HashUtil;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Utils\CartLocalStore;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomLinkHelper;
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
    private $_miniCartModel; // cache


    public function __construct()
    {
        /**
         * array, each entry having the following structure
         *      - 0: options
         *      - 1: cartModel
         *
         * If the options match, then use the cartModel
         */
        $this->_cartModel = [];
        $this->sessionName = 'cart';
        $this->className = 'CartLayer';
        $this->moduleName = 'Ekom';

    }


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
//        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $id => $item) {
//            $ret[$id] = $item['quantity'];
//        }
//        return $ret;
//    }
//
//    public function getProductQuantity($productId, $default = 0)
//    {
//        $this->initSessionCart();
//        $shopId = ApplicationRegistry::get("ekom.shop_id");
//        if (array_key_exists($productId, $_SESSION['ekom'][$this->sessionName][$shopId]['items'])) {
//            return $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$productId]['quantity'];
//        }
//        return $default;
//    }


    /**
     * @todo-ling: this is deprecated, fix binding with ekomCardCombination module
     * when you remove it...
     */
    public function getItemsExtraArgs($productIdentity, $argName, $default = null)
    {
        $productIdentity = (int)$productIdentity;
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $items = $_SESSION['ekom'][$this->sessionName][$shopId]['items'];
        foreach ($items as $item) {
            if ($item['id'] === $productIdentity) {
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


    /**
     * @deprecated
     * Note: this method doesn't work for product with details
     */
    public function addItems(array $productId2Qty)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");

        foreach ($productId2Qty as $productId => $qty) {

            $productId = (string)$productId;
            $alreadyExists = false;
            foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
                if ((string)$item['id'] === $productId) {
                    $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'] += $qty;
                    $alreadyExists = true;
                    break;
                }
            }

            if (false === $alreadyExists) {
                $_SESSION['ekom'][$this->sessionName][$shopId]['items'][] = [
                    "quantity" => $qty,
                    "id" => $productId,
                ];
            }
        }


        $this->writeToLocalStore();
    }

    public function getItems()
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        return $_SESSION['ekom'][$this->sessionName][$shopId]['items'];
    }


    public function getQuantity($productIdentity)
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            if ($item['id'] === $productIdentity) {
                return $item['quantity'];
            }
        }
        return 0;
    }


    public function setCartContent(array $cart, $shopId = null)
    {
        $this->initSessionCart();
        if (null === $shopId) {
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        $_SESSION['ekom'][$this->sessionName][$shopId] = $cart;
        $this->writeToLocalStore();
    }

    public function getCartContent($shopId = null)
    {
        $this->initSessionCart();
        if (null === $shopId) {
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        return $_SESSION['ekom'][$this->sessionName][$shopId];
    }


    /**
     * @todo-ling: consider that extraArgs comes from post or get (the user),
     * and might be very heavy, don't you want to limit the size of extraArgs?
     *
     *
     * This system recognizes the following keys:
     *
     * - id
     * - quantity
     * - ?details
     *      - major
     *      - ?minor
     *
     * The idea behind extraArgs is to extend this system in the future.
     * For now, extraArgs is the holder/transporter for the details key.
     *
     * @throws EkomUserMessageException when something wrong happens
     *
     */
    public function addItem($qty, $productId, array $extraArgs = [])
    {
        $this->initSessionCart();


        $details = array_key_exists('details', $extraArgs) ? $extraArgs['details'] : [];
        $shopId = ApplicationRegistry::get("ekom.shop_id");


        $majorDetailsParams = array_key_exists('major', $details) ? $details['major'] : [];


        $token = CartUtil::generateTokenByProductIdMajorProductDetails($productId, $majorDetailsParams);


//        $this->sanitizeExtraArgs($extraArgs);


        $alreadyExists = false;
        $remainingStockQty = null;
        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
            if ((string)$item['id'] === $token) {


                $isConfigurable = $this->isConfigurableProduct($productId, $details);
                if (false === $isConfigurable) {

                    $existingQuantity = $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'];
                    $this->checkQuantityOverflow($productId, $existingQuantity, $qty, $details);

                    $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'] += $qty;
                    if ($details) {
                        $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['details'] = $details;
                    }
                    $alreadyExists = true;
                    break;
                } else {
                    $this->checkQuantityOverflow($productId, 0, $qty, $details);
                    $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'] = $qty;
                    $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['details'] = $details;
                    $alreadyExists = true;
                    break;
                }
            }
        }

        if (false === $alreadyExists) {

            $this->checkQuantityOverflow($productId, 0, $qty, $details);

            $arr = [
                "quantity" => $qty,
                "id" => $token,
            ];


            if (count($details) > 0) {
                $arr['details'] = $details;
            }
            $_SESSION['ekom'][$this->sessionName][$shopId]['items'][] = $arr;
        }


        // modules might have change the session even if this method didn't add the item.
        $this->writeToLocalStore();
    }


    public function removeItem($token)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $token = (string)$token;
        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
            if ((string)$item['id'] === $token) {
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
     * and return the number of that products put in the cart.
     *
     * UPDATE: 2017-08-10: I believe it should be (the stock) checked elsewhere, not in this
     * class. @todo-ling: remove the "database fetching" code in this method.
     *
     * @return false|true|int,
     *          if false, a problem occurred, you can get the error with the errors array.
     *          if true, it means the quantity has been added to the cart.
     *          if int, represents the quantity that has been put into the cart.
     *                  Note: if acceptOutOfStockOrders is true, then any quantity will be accepted.
     *                  If acceptOutOfStockOrders is false, then if there is 7 products left and you order 10,
     *                  it will return 7.
     *
     * @throws EkomUserMessageException when something wrong happens
     */
    public function updateItemQuantity($token, $newQty)
    {
        $this->initSessionCart();
        $token = (string)$token;
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $productId = $this->getProductIdByCartToken($token);


        $newQty = (int)$newQty;
        if ($newQty < 0) {
            $newQty = 0;
        }

        $wasUpdated = false;
        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
            if ((string)$item['id'] === $token) {
                $existingQty = $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'];
                $details = $this->getProductDetailsByToken($token);
                $this->checkQuantityOverflow($productId, $existingQty, $newQty, $details, true);

                $_SESSION['ekom'][$this->sessionName][$shopId]['items'][$k]['quantity'] = $newQty;
                $wasUpdated = true;
                break;
            }
        }


        $this->writeToLocalStore();
        return (true === $wasUpdated);
    }

    public function getIdentityString($productId, array $detailsParams)
    {
        if (false !== ($idString = $this->getIdentityStringHashByDetails($detailsParams))) {
            return $productId . "-" . $idString;
        }
        return $productId;
    }


    public function prepareUserCart()
    {
        if (null !== ($userId = SessionUser::getValue('id'))) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");

            if (false === array_key_exists('ekom', $_SESSION)) {
                $_SESSION['ekom'] = [];
            }


            if (false === array_key_exists($this->sessionName, $_SESSION['ekom']) || false === array_key_exists($shopId, $_SESSION['ekom'][$this->sessionName])) {
                // the user doesn't have a session cart yet, we create it from the localStorage if any.

                $userCart = $this->getCartLocalStore()->getUserCart($userId, $shopId);
                $_SESSION['ekom'][$this->sessionName][$shopId] = $userCart;

            } else {
                // the user already has a cart in session, she will use it, we don't need to do anything
            }
        } else {
            XLog::error("[$this->moduleName] - $this->className.prepareUserCart: SessionUser not connected or doesn't have an id. sessionDump: " . ArrayToStringTool::toPhpArray($_SESSION));
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


    public function getTotalWeight()
    {
        $model = $this->getCartModel();
        return $model['totalWeight'];
    }



//    public function tryAddCouponByCode($code)
//    {
//        $this->initSessionCart();
//        $shopId = ApplicationRegistry::get("ekom.shop_id");
//        return $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'];
//    }


    public function getCouponBag()
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        return $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'];
    }


    /**
     * @param array $bag , array of coupon ids
     * @return $this
     */
    public function setCouponBag(array $bag)
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $_SESSION['ekom'][$this->sessionName][$shopId]['coupons'] = $bag;
        return $this;
    }


    public function clean()
    {
        $_SESSION['ekom'][$this->sessionName] = [];
    }

    public function getContext()
    {
        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        return $_SESSION['ekom'][$this->sessionName][$shopId];
    }


    public function getCartItemByToken($token)
    {
        $this->initSessionCart();
        $token = (string)$token;
        $shopId = ApplicationRegistry::get("ekom.shop_id");

        foreach ($_SESSION['ekom'][$this->sessionName][$shopId]['items'] as $k => $item) {
            if ((string)$item['id'] === $token) {
                return $item;
            }
        }
        return false;
    }

    public function getCartModelByItems(array $items, $useEstimateShippingCosts = true, $couponBag = null)
    {

        $this->initSessionCart();
        $model = [];
        $modelItems = [];
        $totalQty = 0;
        $totalWeight = 0;
        $linesTotalWithoutTax = 0;
        $linesTotalWithTax = 0;


        $isB2b = E::isB2b();
        //--------------------------------------------
        // CALCULATING LINE PRICES AND TOTAL
        //--------------------------------------------

        foreach ($items as $item) {

            $cartToken = $item['id'];
            $productId = $this->getProductIdByCartToken($cartToken);


            $details = (array_key_exists('details', $item)) ? $item['details'] : [];
            $productDetails = CartUtil::getMergedProductDetails($details);


            if (false !== ($it = $this->getCartItemInfo($productId, $productDetails))) {


                $qty = $item['quantity'];
                $weight = $it['weight'];


                if (false === array_key_exists('errorCode', $it)) {


                    $it['cartToken'] = $cartToken;

                    $it['productCartDetails'] = $details;


                    $it['quantity'] = $qty;
                    $totalQty += $qty;
                    $totalWeight += $weight * $qty;

                    $uriDetails = UriTool::uri($it['uri_card_with_ref'], $productDetails, true);
                    $it['uri_card_with_details'] = $uriDetails;


                    $linePriceWithoutTax = $qty * $it['rawSalePriceWithoutTax'];
                    $linePriceWithTax = $qty * $it['rawSalePriceWithTax'];

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
                        $it['rawLinePrice'] = $it['rawLinePriceWithoutTax'];
                        $it['linePrice'] = $it['linePriceWithoutTax'];
                    } else {
                        $it['rawLinePrice'] = $it['rawLinePriceWithTax'];
                        $it['linePrice'] = $it['linePriceWithTax'];
                    }

//                $attrValues = [];
//                foreach ($it['attributes'] as $v) {
//                    $attrValues[] = $v['value'];
//                }
//                $it['attributeValues'] = $attrValues;


                    $modelItems[] = $it;
                } else {
                    XLog::error("[$this->moduleName] - $this->className.getCartModelByItems: errorCode: " . $it['errorCode'] . ", msg: " . $it['errorMessage']);
                }
            }
        }

        $totalWeight = round($totalWeight, 2);

//        echo '<hr>';
//        az($items);


        $taxAmount = $linesTotalWithTax - $linesTotalWithoutTax;


        $model['isB2B'] = $isB2b;
        $model['totalQuantity'] = $totalQty;
        $model['totalWeight'] = $totalWeight;
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
        // ADDING/RECHECKING COUPONS
        //--------------------------------------------
        $couponApi = EkomApi::inst()->couponLayer();
        $validCoupons = [];

        if (null === $couponBag) {
            $couponBag = $this->getCouponBag();
        }

        $details = $couponApi->applyCouponBag($linesTotalWithoutTax, $linesTotalWithTax, "beforeShipping", $couponBag, $validCoupons);
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
        EkomApi::inst()->initWebContext();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
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
    private function getProductIdByCartToken($productIdentity)
    {
        return explode('-', $productIdentity)[0];
    }

    private function getIdentityStringHashByDetails(array $details)
    {
        if (count($details) > 0) {
            ksort($details);

//            $s = "";
//            foreach ($details as $k => $v) {
//                if (is_array($v)) {
//                    foreach ($v as $k2 => $v2) {
//                        $s .= $k2 . "_$v2-";
//                    }
//                } else {
//
//                    $s .= $k . "_$v-";
//                }
//            }
//            return $s;

            $sDetails = serialize($details);
            return preg_replace('![^a-zA-Z0-9]!', '-', $sDetails);
            return hash('ripemd160', $sDetails);
        }
        return false;
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

    private function doGetCartModel(array $options = null)
    {

        /**
         * Note: maybe the cache should be different for mini and not mini cart model.
         * But in the current implementation, those are the same...
         */
        // cache?
        foreach ($this->_cartModel as $item) {
            list($_options, $_model) = $item;
            if ($_options === $options) {
                return $_model;
            }
        }


        if (null === $options) {
            $useEstimateShippingCosts = true;
            $items = null;
        } else {
            $useEstimateShippingCosts = (array_key_exists("useEstimateShippingCosts", $options) && true === $options['useEstimateShippingCosts']);
            $items = (array_key_exists('items', $options)) ? $options["items"] : null;
        }

        $this->initSessionCart();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        if (null === $items) {
            $items = $_SESSION['ekom'][$this->sessionName][$shopId]['items'];
        }
        $ret = $this->getCartModelByItems($items, $useEstimateShippingCosts);


        // cache for next time
        $this->_cartModel[] = [$options, $ret];

        return $ret;
    }


    /**
     * @param $pId : int, the product id
     * @return array: the cartItem model
     */
    private function getCartItemInfo($pId, array $productDetails = [])
    {
        $pId = (int)$pId;
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");
        $sDetails = HashUtil::createHashByArray($productDetails);
        $b2b = (int)E::isB2b();

        return A::cache()->get("Module.Ekom.Api.Layer.$this->className.getCartItemInfo.$shopId.$langId.$pId.$sDetails.$b2b"  , function () use ($pId, $shopId, $langId, $productDetails) {


            $b = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($pId, $shopId, $langId, $productDetails);

            if (array_key_exists('errorCode', $b)) {
                XLog::error("[$this->moduleName] - $this->className.getCartItemInfo: product not found or request failed with product id: $pId");
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
                                "name" => $name,
                                "label" => $info['label'],
                                "value" => $val['value'],
                                "value_label" => $val['value_label'],
                            ];
                            break;
                        }
                    }
                }


                $mainImage = "";
                $imageThumb = "";
                $imageSmall = "";
                $imageMedium = "";
                $imageLarge = "";

                if (count($b['images']) > 0) {
                    $defaultImage = $b['defaultImage'];
                    $imgs = $b['images'][$defaultImage];
                    $imageThumb = $imgs['thumb'];
                    $imageSmall = $imgs['small'];
                    $imageMedium = $imgs['medium'];
                    $imageLarge = $imgs['large'];
                    //
                    $mainImage = $imgs['thumb'];
                }


                $uriRef = E::link("Ekom_productCardRef", ['slug' => $cardSlug, 'ref' => $b["ref"]]);


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
                    'uri_card_with_ref' => $uriRef,
                    'product_card_id' => $productCardId,
                    'attributes' => $zeAttr,
//                    'attributeDetails' => $zeAttr,
                    'price' => $b['price'],
                    'rawPrice' => $b['rawPrice'],
                    'salePrice' => $b['salePrice'],
                    'rawSalePrice' => $b['rawSalePrice'],
//                    'salePriceWithTax' => $b['salePriceWithTax'],
//                    'salePriceWithoutTax' => $b['salePriceWithoutTax'],
                    'image' => $mainImage,
                    'imageThumb' => $imageThumb,
                    'imageSmall' => $imageSmall,
                    'imageMedium' => $imageMedium,
                    'imageLarge' => $imageLarge,
                    'outOfStockText' => $b['outOfStockText'],
                    'taxDetails' => $b['taxDetails'],

//                    'rawSalePriceWithoutTax' => $b['rawSalePriceWithoutTax'],
//                    'rawSalePriceWithTax' => $b['rawSalePriceWithTax'],
                ]);
            }

        }, EkomApi::inst()->productLayer()->getProductBoxModelCaches());
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
     * @param array $majorDetailsParams
     * @param bool $isUpdate
     * @throws EkomUserMessageException
     */
    private function checkQuantityOverflow($productId, $existingQty, $qty, array $details, $isUpdate = false)
    {
        if (false === E::conf('acceptOutOfStockOrders', false)) {

            $productDetails = CartUtil::getMergedProductDetails($details);
            $boxModel = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($productId, null, null, $productDetails);


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


//    private function getUniqueProductId($productId, $complementaryId = null)
//    {
//        $o = X::get("Ekom_productIdToUniqueProductId");
//        /**
//         * @var $o ProductIdToUniqueProductIdAdaptor
//         */
//        return $o->getUniqueProductId($productId, $complementaryId);
//    }

//    private function sanitizeExtraArgs(array &$extraArgs)
//    {
//        $allowedExtraArgs = ['details'];
//        Hooks::call("Ekom_feedCartAllowedExtraArgs", $allowedExtraArgs);
//        foreach ($extraArgs as $k => $v) {
//            if (false === array_key_exists($k, $allowedExtraArgs)) {
//                unset($extraArgs, $k);
//            }
//        }
//    }


    private function isConfigurableProduct($productId, array $details)
    {
        return (
            array_key_exists('minor', $details) &&
            count($details['minor']) > 0 // assuming it's an array already..
        );
    }

}
