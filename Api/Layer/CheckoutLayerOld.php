<?php


namespace Module\Ekom\Api\Layer;

use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Core\Services\X;
use Ingenico\Handler\IngenicoHandler;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Exception\IncompleteOrderException;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Status\Action\EkomStatusAction;
use Module\Ekom\Utils\E;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use QuickPdo\QuickPdo;


class CheckoutLayerOld
{

    /**
     * Whether or not carrier will allow communication with external apis.
     * Is only used once when the order is placed for good.
     */
    private $forReal;

    public function __construct()
    {
        $this->forReal = false;
    }


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
                $a = EkomSession::get('order.singleAddress');

                $billingAddressId = $a["billing_address_id"];
                $shippingAddressId = $a["shipping_address_id"];
                $carrierId = $a["carrier_id"];
                $paymentMethodId = $a["payment_method_id"];
                $paymentMethodOptions = $a["payment_method_options"];


                $userLayer = $api->userLayer();
                $userAddressLayer = $api->userAddressLayer();
                $carrierLayer = $api->carrierLayer();
                $couponLayer = $api->couponLayer();
                $cartLayer = $api->cartLayer();
                $paymentLayer = $api->paymentLayer();


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


                $cartModel = EkomApi::inst()->cartLayer()->getCartModel([
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
                    $shippingCosts = $carrierLayer->calculateShippingCostByCarrierId($carrierId, $productInfos, $shippingAddress);
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
                $form = $provider->getForm("Ekom", "UserAddress");
                $hasCarrierChoice = $carrierLayer->useSingleCarrier();
                $paymentMethodBlocks = $paymentLayer->getShopPaymentMethodBlockModels(null, $paymentMethodId, $paymentMethodOptions);
                $paymentMethod = null;
                if (null !== $paymentMethodId) {
                    $paymentMethod = $paymentLayer->getConfiguredPaymentBlockModel($paymentMethodId, $paymentMethodOptions);
                }


//                $currentStep = $a["current_step"];


                $cartModel = EkomApi::inst()->cartLayer()->getCartModel();

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
                    "shippingAddresses" => $addresses,
                    "selectedShippingAddressId" => $shippingAddressId,
                    "defaultCountry" => $countryId,
                    "shippingAddressFormModel" => $form->getModel(),
                    "useSingleCarrier" => $hasCarrierChoice,
                    "paymentMethodBlocks" => $paymentMethodBlocks,
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
                    "paymentMethod" => $paymentMethod, // or null
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


                return $model;
            }
        }
        return false;
    }


    public function setShippingAddressId($id, array $options = null)
    {
        $this->setSessionValue("shipping_address_id", $id, $options);
    }

    public function setPaymentMethod($id, array $paymentMethodOptions = [], array $options = null)
    {
        $this->setSessionValue("payment_method", [$id, $paymentMethodOptions], $options);
    }


    /**
     * Used by EkomEstimate module
     */
    public function getPlaceOrderInfo()
    {

        $checkoutMode = E::conf("checkoutMode");
        if ('singleAddress' === $checkoutMode) {

            EkomApi::inst()->initWebContext();

            $userId = EkomApi::inst()->userLayer()->getUserId();
            $this->forReal = true;
            $model = $this->getOrderModel();
            $this->forReal = false;


            // tmp
            $model['paymentMethod'] = "creditCard";


            if (null === $model['paymentMethod']) {
                throw new IncompleteOrderException("Incomplete order: missing paymentMethod");
            }


            $userLayer = EkomApi::inst()->userLayer();
            $shopLayer = EkomApi::inst()->shopLayer();


            $shopId = (int)ApplicationRegistry::get("ekom.shop_id");


            $userGroups = $userLayer->getUserGroupNames($userId);
            $userInfo = $userLayer->getUserInfo($userId);
            $userInfo['groups'] = $userGroups;


            $shopInfo = $shopLayer->getShopInfoById($shopId);
            $shopAddress = $shopLayer->getShopPhysicalAddress($shopId);
            $shopInfo['address'] = $shopAddress;


            $invoiceAddress = $model['shippingAddress'];
            $billingAddress = $model['billingAddress'];

            $details = $model;
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
            unset($details['paymentMethodId']);
            unset($details['paymentMethodOptions']);

            return [
                'user_id' => $userId,
                'reference' => EkomApi::inst()->orderLayer()->getUniqueReference(),
                'date' => date("Y-m-d H:i:s"),
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


    public function placeOrder($cleanOnSuccess = true)
    {
        try {

            $ret = QuickPdo::transaction(function () use ($cleanOnSuccess) {

                $info = $this->getPlaceOrderInfo();

                if (false !== ($orderId = EkomApi::inst()->order()->create([
                        'user_id' => $info['user_id'],
                        'reference' => $info['reference'],
                        'date' => $info['date'],
                        'tracking_number' => $info['tracking_number'],
                        'user_info' => $info['user_info'],
                        'shop_info' => $info['shop_info'],
                        'shipping_address' => $info['shipping_address'],
                        'billing_address' => $info['billing_address'],
                        'order_details' => $info['order_details'],
                    ]))
                ) {


                    EkomApi::inst()->orderLayer()->addOrderStatusByEkomAction($orderId, EkomStatusAction::ACTION_ORDER_PLACED);

                    if (true === $cleanOnSuccess) {
                        EkomApi::inst()->cartLayer()->clean();
                        $this->cleanSessionOrder();
                        $_SESSION['ekom.order.last'] = $orderId;
                    }
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
        EkomSession::remove("order.singleAddress");
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getCurrentStep()
    {

    }


    private function setSessionValue($key, $value, array $options = null)
    {
        if ('singleAddress' === E::conf("checkoutMode")) {

            $a = EkomSession::get("order.singleAddress");

            switch ($key) {
                case 'shipping_address_id':
                    $a["shipping_address_id"] = $value;
                    break;
                case 'payment_method':
                    list($id, $paymentMethodOptions) = $value;
                    $a["payment_method_id"] = $id;
                    $a["payment_method_options"] = $paymentMethodOptions;
                    break;
                default:
                    break;
            }

            if (null !== $options) {
                if (array_key_exists("marker", $options) && null !== $options['marker']) {
                    $a["current_step"] = $options["marker"];
                }
                if (array_key_exists("saveAsDefault", $options) && true === (bool)$options['saveAsDefault']) {
                    switch ($key) {
                        case 'shipping_address_id':
                            $userId = E::getUserId();
                            EkomApi::inst()->userAddressLayer()->setDefaultShippingAddress($value, $userId);
                            break;
                        default:
                            throw new \Exception("Unknown key: $key");
                            break;
                    }
                }
            }

            EkomSession::set("order.singleAddress", $a);

        } else {
            throw new \Exception("Not implemented yet");
        }
    }

    private function initOrderModel()
    {
        SessionTool::start();

        if ('singleAddress' === E::conf("checkoutMode")) {
            if (false === EkomSession::has("order.singleAddress")) {
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
                 * --------- payment_method_id
                 * --------- ?payment_method_options: array of key => value, depending on the payment method (read relevant payment method doc for more info)
                 *
                 *
                 */
                $userAddressLayer = EkomApi::inst()->userAddressLayer();
                $userId = E::getUserId();
                $billingAddress = $userAddressLayer->getDefaultBillingAddress($userId);
                $shippingAddress = $userAddressLayer->getDefaultShippingAddress($userId);


                $shippingAddressId = null;
                if (false !== $shippingAddress) {
                    $shippingAddressId = $shippingAddress['address_id'];
                }


                $billingAddressId = null;
                if (false !== $billingAddress) {
                    $billingAddressId = $billingAddress['address_id'];
                }

                // choose default carrier if none is set
                $carrierId = null;
                if (false !== ($id = EkomApi::inst()->carrierLayer()->getDefaultCarrierId())) {
                    $carrierId = $id;
                }


                EkomSession::set('order.singleAddress', [
                    "billing_address_id" => $billingAddressId,
                    "shipping_address_id" => $shippingAddressId,
                    "carrier_id" => $carrierId,
                    "payment_method_id" => null,
                    "payment_method_options" => null,
                    "current_step" => 0,
                ]);
            }
        } else {
            throw new \Exception("Not implemented yed");
        }
    }


}
