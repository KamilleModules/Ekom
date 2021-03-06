<?php


namespace Module\Ekom\Api\Layer;

use Authenticate\SessionUser\SessionUser;
use Bat\ArrayTool;
use Bat\SessionTool;
use Core\Services\Hooks;
use Core\Services\X;
use Ecp\Exception\EcpUserMessageException;
use Ingenico\Handler\IngenicoHandler;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Exception\IncompleteOrderException;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Carrier\CarrierInterface;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Status\Action\EkomStatusAction;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\CheckoutOrder\CheckoutOrderUtil;
use Module\Ekom\Utils\E;
use Module\ThisApp\Ekom\Utils\CheckoutPage\CheckoutPageUtil;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use QuickPdo\QuickPdo;

/**
 * Be sure to read the following first:
 * @link https://github.com/KamilleModules/Ekom/tree/master/doc/checkout/checkout-placeorder-and-currentcheckoutdata.md
 *
 */
class CheckoutLayer
{


    private $_cartLayer;
    protected $sessionName;
    protected $usePayment;
    protected $uniqueReferenceType;


    public function __construct()
    {
        $this->sessionName = 'order.singleAddress';
        $this->uniqueReferenceType = 'ekom';
        $this->usePayment = true;
    }

    /**
     *
     * This method places an order in the database and returns the id of the order.
     * It
     *
     *
     * @param array $data
     * - user_id
     * - shop_id
     * - lang_id
     * - currency_id
     * - ?carrier_id              (only if at least an item needs to be shipped)
     * - ?shipping_address_id    (only if at least an item needs to be shipped)
     * - ?shop_address_id        (only if the shipping_address_id is defined)
     * - billing_address_id
     * - payment_method_id
     *
     *
     * @param array $cartModel
     * @see EkomModels::cartModel()
     * @return int, the newly created order id
     *
     * @throws \Exception
     */
    public function placeOrder(array $data, array $cartModel)
    {
        return CheckoutOrderUtil::create()
            ->setTestMode(false)
            ->placeOrder($data, $cartModel);
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @return false|array orderModel,
     *              return false if the user is not connected
     *
     */
    public function getOrderModel()
    {

        $checkoutMode = E::conf("checkoutMode");
        if (SessionUser::isConnected()) {


            if ('singleAddress' === $checkoutMode) {


                $api = EkomApi::inst();
                $api->initWebContext();
                $langId = (int)ApplicationRegistry::get("ekom.lang_id");


                // start collecting order data
                $this->initOrderModel();

                // taking data out of sections
                $a = EkomSession::get($this->sessionName);

                $billingAddressId = $a["billing_address_id"];
                $shippingAddressId = $a["shipping_address_id"];
                $shippingComment = $a["shipping_comment"];
                $carrierName = $a["carrier_name"];


                $paymentMethodId = $a["payment_method_id"];
                $paymentMethodOptions = $a["payment_method_options"];

                // throws an exception if the payment method doesn't exist, because
                // this probably is a hack (or probably not?)
                $paymentMethodName = EkomApi::inst()->paymentLayer()->getPaymentMethodNameById($paymentMethodId);
                if (false === $paymentMethodName) {
                    $msg = "No payment method name found with id: $paymentMethodId";
                    throw new EkomException($msg);
                }


                $userLayer = $api->userLayer();
                $userAddressLayer = $api->userAddressLayer();
                $carrierLayer = $api->carrierLayer();
                $couponLayer = $api->couponLayer();
                $cartLayer = $this->getCartLayer();


                $userId = SessionUser::getValue("id");
                $addresses = $userAddressLayer->getUserAddresses($userId, $langId);


                /**
                 * false|addressModel
                 */
                $billingAddress = false;
                $shippingAddress = false;
                foreach ($addresses as $address) {
                    if ((int)$billingAddressId === (int)$address['address_id']) {
                        $billingAddress = $address;
                    }
                    if ((int)$shippingAddressId === (int)$address['address_id']) {
                        $shippingAddress = $address;
                    }
                }


                $countryId = $userLayer->getUserPreferredCountry();


                $cartModel = $cartLayer->getCartModel([
                    'useEstimateShippingCosts' => false,
                ]);

                $productInfos = $cartModel['items'];

                /**
                 * the user doesn't have an address yet.
                 */
                if (false === $shippingAddress) {
                    $shippingCosts['rawTotalShippingCost'] = 0;
                    $shippingCosts['totalShippingCost'] = '--'; // means not set for templates
                    $shippingCosts['sections'] = [];
                    $shippingCosts['notHandled'] = [];
                } else {
                    $shippingCosts = $carrierLayer->calculateShippingCostByCarrierName($carrierName, $productInfos, $shippingAddress);
                    $shippingCosts['rawTotalShippingCost'] = $shippingCosts['totalShippingCost'];
                    $shippingCosts['totalShippingCost'] = E::price($shippingCosts['rawTotalShippingCost']);
                }


                // in singleAddress mode, we only have one order section
                $_orderSectionSubtotalWithoutTax = $cartModel['rawCartTotalWithoutTax'] + $shippingCosts['rawTotalShippingCost'];
                $_orderSectionSubtotalWithTax = $cartModel['rawCartTotalWithTax'] + $shippingCosts['rawTotalShippingCost'];
                $validCoupons = [];
                $data = [];// cartItems?


                $details = $couponLayer->applyCouponBag($_orderSectionSubtotalWithoutTax, $_orderSectionSubtotalWithTax, "afterShipping", $cartLayer->getCouponBag(), $validCoupons, $data);
                $_orderSectionTotalWithoutTax = $details['rawDiscountPrice'];
                $_orderSectionTotalWithTax = $details['rawDiscountPriceWithTax'];
                $_orderGrandTotalWithoutTax = $_orderSectionTotalWithoutTax;
                $_orderGrandTotalWithTax = $_orderSectionTotalWithTax;


                $orderSectionSubtotalWithoutTax = E::price($_orderSectionSubtotalWithoutTax);
                $orderSectionTotalWithoutTax = E::price($_orderSectionTotalWithoutTax);
                $orderGrandTotalWithoutTax = E::price($_orderGrandTotalWithoutTax);

                $orderSectionSubtotalWithTax = E::price($_orderSectionSubtotalWithTax);
                $orderSectionTotalWithTax = E::price($_orderSectionTotalWithTax);
                $orderGrandTotalWithTax = E::price($_orderGrandTotalWithTax);

                /**
                 * @var $provider OnTheFlyFormProviderInterface
                 */
                $provider = X::get("Core_OnTheFlyFormProvider");
//                $form = $provider->getForm("Ekom", "UserAddress");
                $hasCarrierChoice = $carrierLayer->useSingleCarrier();


//                $paymentMethodBlocks = $paymentLayer->getShopPaymentMethodBlockModels(null, $paymentMethodId, $paymentMethodOptions);
//                $paymentMethod = null;
//                if (null !== $paymentMethodId) {
//                    $paymentMethod = $paymentLayer->getConfiguredPaymentBlockModel($paymentMethodId, $paymentMethodOptions);
//                }


//                $currentStep = $a["current_step"];


                $cartModel = $cartLayer->getCartModel();

                $isB2b = $cartModel['isB2B'];


                $_couponTotalSavingWithoutTax = 0;
                $_couponTotalSavingWithoutTax += $cartModel['rawTotalSavingWithoutTax']; // target:beforeShipping
                $_couponTotalSavingWithoutTax += $details['rawTotalSaving']; // target:afterShipping
                $couponTotalSavingWithoutTax = E::price(-$_couponTotalSavingWithoutTax);

                $_couponTotalSavingWithTax = 0;
                $_couponTotalSavingWithTax += $cartModel['rawTotalSavingWithTax'];
                $_couponTotalSavingWithTax += $details['rawTotalSavingWithTax'];
                $couponTotalSavingWithTax = E::price(-$_couponTotalSavingWithTax);


                $model = [
                    "checkoutMode" => $checkoutMode,
                    "isB2B" => $cartModel['isB2B'],
                    "billingAddress" => $billingAddress, // or false
                    "shippingAddress" => $shippingAddress, // or false
                    "shippingComment" => $shippingComment,
                    "shippingAddresses" => $addresses,
                    "selectedShippingAddressId" => $shippingAddressId,
                    "defaultCountry" => $countryId,
//                    "shippingAddressFormModel" => $form->getModel(),
                    "useSingleCarrier" => $hasCarrierChoice,
//                    "paymentMethodBlocks" => $paymentMethodBlocks,
//                    "currentStep" => $currentStep,
                    //
                    "orderSectionSubtotalWithoutTax" => $orderSectionSubtotalWithoutTax,
                    "rawOrderSectionSubtotalWithoutTax" => $_orderSectionSubtotalWithoutTax,
                    "orderSectionTotalWithoutTax" => $orderSectionTotalWithoutTax,
                    "rawOrderSectionTotalWithoutTax" => $_orderSectionTotalWithoutTax,
                    "orderGrandTotalWithoutTax" => $orderGrandTotalWithoutTax,
                    "rawOrderGrandTotalWithoutTax" => $_orderGrandTotalWithoutTax,
                    //
                    "orderSectionSubtotalWithTax" => $orderSectionSubtotalWithTax,
                    "rawOrderSectionSubtotalWithTax" => $_orderSectionSubtotalWithTax,
                    "orderSectionTotalWithTax" => $orderSectionTotalWithTax,
                    "rawOrderSectionTotalWithTax" => $_orderSectionTotalWithTax,
                    "orderGrandTotalWithTax" => $orderGrandTotalWithTax,
                    "rawOrderGrandTotalWithTax" => $_orderGrandTotalWithTax,
                    //


                    "beforeShippingCoupons" => $cartModel['coupons'],
                    "afterShippingCouponDetails" => $details,

                    "couponTotalSavingWithoutTax" => $couponTotalSavingWithoutTax,
                    "rawCouponTotalSavingWithoutTax" => $_couponTotalSavingWithoutTax,

                    "couponTotalSavingWithTax" => $couponTotalSavingWithTax,
                    "rawCouponTotalSavingWithTax" => $_couponTotalSavingWithTax,

                    "paymentMethodId" => $paymentMethodId,
                    "paymentMethodOptions" => $paymentMethodOptions,
                    "paymentMethodName" => $paymentMethodName,
                    "orderSections" => $shippingCosts,
                    //
                    "taxAmount" => $cartModel['taxAmount'],
                    "rawTaxAmount" => $cartModel['rawTaxAmount'],
                    "linesTotal" => $cartModel['linesTotal'],
                    "rawLinesTotal" => $cartModel['rawLinesTotal'],
                    "linesTotalWithoutTax" => $cartModel['linesTotalWithoutTax'],
                    "rawLinesTotalWithoutTax" => $cartModel['rawLinesTotalWithoutTax'],
                    "linesTotalWithTax" => $cartModel['linesTotalWithTax'],
                    "rawLinesTotalWithTax" => $cartModel['rawLinesTotalWithTax'],
                    "cartTotal" => $cartModel['cartTotal'],
                    "rawCartTotal" => $cartModel['rawCartTotal'],
                    "cartTotalWithTax" => $cartModel['cartTotalWithTax'],
                    "rawCartTotalWithTax" => $cartModel['rawCartTotalWithTax'],
                    "cartTotalWithoutTax" => $cartModel['cartTotalWithoutTax'],
                    "rawCartTotalWithoutTax" => $cartModel['rawCartTotalWithoutTax'],
//                            "shippingType" => "singleAddressShipping", // singleAddressShipping|multipleAddressShipping // implicitly: it's singleAddressShipping, unless otherwise specified
                ];


                if (true === $isB2b) {
                    $model["orderSectionSubtotal"] = $orderSectionSubtotalWithoutTax;
                    $model["rawOrderSectionSubtotal"] = $_orderSectionSubtotalWithoutTax;
                    $model["orderSectionTotal"] = $orderSectionTotalWithoutTax;
                    $model["rawOrderSectionTotal"] = $_orderSectionTotalWithoutTax;
                    $model["orderGrandTotal"] = $orderGrandTotalWithoutTax;
                    $model["rawOrderGrandTotal"] = $_orderGrandTotalWithoutTax;

                    $model["couponTotalSaving"] = $couponTotalSavingWithoutTax;
                    $model["rawCouponTotalSaving"] = $_couponTotalSavingWithoutTax;

                } else {
                    $model["orderSectionSubtotal"] = $orderSectionSubtotalWithTax;
                    $model["rawOrderSectionSubtotal"] = $_orderSectionSubtotalWithTax;
                    $model["orderSectionTotal"] = $orderSectionTotalWithTax;
                    $model["rawOrderSectionTotal"] = $_orderSectionTotalWithTax;
                    $model["orderGrandTotal"] = $orderGrandTotalWithTax;
                    $model["rawOrderGrandTotal"] = $_orderGrandTotalWithTax;

                    $model["couponTotalSaving"] = $couponTotalSavingWithTax;
                    $model["rawCouponTotalSaving"] = $_couponTotalSavingWithTax;
                }


                ksort($model);
                return $model;
            }
        }
        return false;
    }


    /**
     * $additionalOrderDetails: allow to pass payment transaction feedback info.
     *          If the pay_id key is found, it will be used as the pay_identifier.
     */
    public function getPlaceOrderInfo($reference = null, array $additionalOrderDetails = [])
    {

        $checkoutMode = E::conf("checkoutMode");
        if ('singleAddress' === $checkoutMode) {

            

            $userId = EkomApi::inst()->userLayer()->getUserId();
            $model = $this->getOrderModel();


            if (true === $this->usePayment) {
                if (false === array_key_exists('paymentMethodName', $model) || null === $model['paymentMethodName']) {
                    throw new IncompleteOrderException("Incomplete order: missing paymentMethod");
                }
            }


            $userLayer = EkomApi::inst()->userLayer();
            $shopLayer = EkomApi::inst()->shopLayer();


            $shopId = (int)ApplicationRegistry::get("ekom.shop_id");


            $userGroups = $userLayer->getUserGroupNames($userId);
            $userInfo = $userLayer->getUserInfo($userId);
            $userInfo['groups'] = $userGroups;


            $shopInfo = ShopLayer::getShopInfoById($shopId);
            $shopAddress = $shopLayer->getShopPhysicalAddress($shopId);
            $shopInfo['address'] = $shopAddress;


            $invoiceAddress = $model['shippingAddress'];
            $billingAddress = $model['billingAddress'];
            $payIdentifier = array_key_exists('pay_id', $additionalOrderDetails) ? $additionalOrderDetails['pay_id'] : null;


            $paymentDetails = EkomApi::inst()->paymentLayer()->getPaymentDetails($model['paymentMethodName'], $model['paymentMethodOptions']);
            $details = $model;
            $details['payment_details'] = $paymentDetails;
            $details['transaction_feedback'] = $additionalOrderDetails;


            unset($details['checkoutMode']);
            unset($details['billingAddress']);
            unset($details['shippingAddress']);
            unset($details['shippingAddresses']);
            unset($details['selectedShippingAddressId']);
            unset($details['defaultCountry']);
            unset($details['shippingAddressFormModel']);
            unset($details['useSingleCarrier']);
            unset($details['paymentMethodBlocks']);
            unset($details['currentStep']);
//            unset($details['paymentMethodId']);
//            unset($details['paymentMethodOptions']);


            if (null === $reference) {
                $reference = EkomApi::inst()->orderLayer()->getUniqueReference($this->uniqueReferenceType);
            }

            return [
                'user_id' => $userId,
                'reference' => $reference,
                'date' => date("Y-m-d H:i:s"),
                'pay_identifier' => $payIdentifier,
                'tracking_number' => $model['orderSections']['sections'][0]['trackingNumber'],
                'user_info' => serialize($userInfo),
                'shop_info' => serialize($shopInfo),
                'shipping_address' => serialize($invoiceAddress),
                'billing_address' => serialize($billingAddress),
                'order_details' => serialize($details),
            ];

        } else {
            throw new EkomApiException("Unknown mode: $checkoutMode");
        }
    }


    /**
     * @param null $reference
     * @param array $additionalOrderDetails , see get getPlaceOrderInfo for more details
     * @param bool $cleanOnSuccess
     * @return bool
     */
    public function placeOrderDeprecated($reference = null, array $additionalOrderDetails = [], $cleanOnSuccess = true)
    {
        try {

            $ret = QuickPdo::transaction(function () use ($cleanOnSuccess, $reference, $additionalOrderDetails) {

                $info = $this->getPlaceOrderInfo($reference, $additionalOrderDetails);
                if (false !== ($orderId = $this->placeOrderByInfo($info))) {

                    $this->onPlaceOrderSuccessAfter($orderId, $info, $cleanOnSuccess);
                }
                return false;
            }, function (\Exception $e) {
                XLog::error("[Ekom module] - CheckoutLayer: $e");
            });
            return $ret;
        } catch (IncompleteOrderException $e) {
            XLog::error("[Ekom module] - CheckoutLayer: $e");
        }

        return false;
    }


    public function cleanSessionOrder()
    {
        EkomSession::remove($this->sessionName);
    }


    /**
     * Helps the cart finding out whether or not an order is being placed.
     * This helps the cart to decide whether or not the shipping info coming
     * from the checkout page should be used to compute the shipping cost.
     *
     */
    public function hasCurrentSessionOrder()
    {
        return EkomSession::has($this->sessionName);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function doGetCartLayer()
    {
        return EkomApi::inst()->cartLayer();
    }


    /**
     * @param array $info
     * @return false|int
     * @throws \Exception
     */
    protected function placeOrderByInfo(array $info)
    {
        return EkomApi::inst()->order()->create([
            'user_id' => $info['user_id'],
            'reference' => $info['reference'],
            'date' => $info['date'],
            'pay_identifier' => (string)$info['pay_identifier'],
            'tracking_number' => $info['tracking_number'],
            'user_info' => $info['user_info'],
            'shop_info' => $info['shop_info'],
            'shipping_address' => $info['shipping_address'],
            'billing_address' => $info['billing_address'],
            'order_details' => $info['order_details'],
        ]);
    }

    protected function onPlaceOrderSuccessAfter($orderId, array $orderInfo, $cleanOnSuccess = true)
    {
        EkomApi::inst()->orderLayer()->addOrderStatusByEkomAction($orderId, EkomStatusAction::ACTION_ORDER_PLACED);

        Hooks::call("Ekom_onPlaceOrderSuccessAfter", $orderId, $orderInfo);


        if (true === $cleanOnSuccess) {
            $this->doGetCartLayer()->clean();
            $this->cleanSessionOrder();

//            EkomApi::inst()->orderBuilderLayer()->get('ekom')->clean();

            EkomSession::set("order.last", $orderId);
            Hooks::call("Ekom_onPlaceOrderCleanedAfter");
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getCartLayer()
    {
        if (null === $this->_cartLayer) {
            $this->_cartLayer = $this->doGetCartLayer();

        }
        return $this->_cartLayer;
    }

    private function initOrderModel()
    {
        SessionTool::start();


        if ('singleAddress' === E::conf("checkoutMode")) {
            if (false === EkomSession::has($this->sessionName)) {
                /**
                 *
                 * class-modules/Ekom/doc/ekom-checkout-synopsis.md
                 *
                 *
                 * - ekom
                 * ----- order.singleAddress
                 * --------- billing_address_id
                 * --------- shipping_address_id
                 * --------- carrier_id
                 * --------- ?carrier_options array of key => value, depending on the carrier (read relevant carrier doc for more info)
                 * --------- shipping_comment: a general comment pertaining to the order (not to a particular address)
                 * --------- shipping_billing_synced: bool, whether or not the shipping and billing address shall be synced.
                 *                                      Note: you might not need this option, depending on your view
                 * --------- payment_method_id
                 * --------- payment_method_options: array of key => value, depending on the payment method (read relevant payment method doc for more info)
                 * --------- payment_method_feedback: array containing the following data:
                 *                                      - ?pay_id: a pay identifier used to identify the payment
                 *                                              might not be used by all PaymentMethodHandlers
                 *                                      - ...other things, depending on the PaymentMethodHandler
                 *
                 *
                 */
                $userAddressLayer = EkomApi::inst()->userAddressLayer();
                $userId = E::getUserId();
                $billingAddress = UserAddressLayer::getPreferredBillingAddress($userId);
                $shippingAddress = UserAddressLayer::getPreferredShippingAddress($userId);


                $shippingAddressId = null;
                if (false !== $shippingAddress) {
                    $shippingAddressId = $shippingAddress['address_id'];
                }


                $billingAddressId = null;
                if (false !== $billingAddress) {
                    $billingAddressId = $billingAddress['address_id'];
                }

                // choose default carrier
                $carrierName = null;
//                if (false !== ($name = EkomApi::inst()->carrierLayer()->getDefaultCarrierName())) {
//                    $carrierName = $name;
//                }
                $carrierName = "default";

                $paymentMethod = null;
                $paymentMethodOptions = null;
                $paymentMethodFeedback = null;
                if (false !== ($row = EkomApi::inst()->paymentLayer()->getDefaultPaymentMethod())) {

                    $paymentMethod = $row['id'];
                    /**
                     * We choose the default options from the model (mvc)
                     */
                    $name = $row['name'];
                    /**
                     * @var $col PaymentMethodHandlerCollectionInterface
                     */
                    $col = X::get("Ekom_getPaymentMethodHandlerCollection");
                    $paymentMethodOptions = $col->get($name)->getDefaultOptions($row['configuration']);
                }


                EkomSession::set($this->sessionName, [
                    //
                    "billing_address_id" => $billingAddressId,
                    "shipping_address_id" => $shippingAddressId,
                    "carrier_name" => $carrierName,
                    "shipping_billing_synced" => false,
                    "shipping_comment" => "",
                    //
                    "payment_method_id" => $paymentMethod,
                    "payment_method_options" => $paymentMethodOptions,
                    "payment_method_feedback" => $paymentMethodFeedback,
                ]);

            }
        } else {
            throw new \Exception("Not implemented yed");
        }
    }


}
