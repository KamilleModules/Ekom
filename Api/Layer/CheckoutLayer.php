<?php


namespace Module\Ekom\Api\Layer;

use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\IncompleteOrderException;
use Module\Ekom\Status\Action\EkomStatusAction;
use Module\Ekom\Utils\E;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use QuickPdo\QuickPdo;


class CheckoutLayer
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

                // start collecting order data
                $this->initOrderModel();


                // taking data out of sections
                $a = $_SESSION['ekom.order.singleAddress'];

                $billingAddressId = $a["billing_address_id"];
                $shippingAddressId = $a["shipping_address_id"];
                $carrierId = $a["carrier_id"];
                $paymentMethodId = $a["payment_method_id"];
                $paymentMethodOptions = $a["payment_method_options"];

                $carrierStep = $a["current_step"];


                $userLayer = $api->userLayer();
                $carrierLayer = $api->carrierLayer();
                $couponLayer = $api->couponLayer();
                $cartLayer = $api->cartLayer();
                $paymentLayer = $api->paymentLayer();


                $userId = SessionUser::getValue("id");
                $shippingAddresses = $userLayer->getUserShippingAddresses($userId);


                /**
                 * false|addressModel
                 */
                $billingAddress = $userLayer->getUserBillingAddressById($userId, $billingAddressId);
                $shippingAddress = $userLayer->getUserShippingAddressById($userId, $shippingAddressId);
                $countryId = $userLayer->getUserPreferredCountry();


                $cartModel = EkomApi::inst()->cartLayer()->getCartModel([
                    'useEstimateShippingCosts' => false,
                ]);

                $productInfos = $cartModel['items'];
                $shippingCosts = $carrierLayer->calculateShippingCostByCarrierId($carrierId, $productInfos, $shippingAddress);
                $shippingCosts['rawTotalShippingCost'] = $shippingCosts['totalShippingCost'];
                $shippingCosts['totalShippingCost'] = E::price($shippingCosts['rawTotalShippingCost']);

                // in singleAddress mode, we only have one order section
                $_orderSectionSubtotal = $cartModel['rawCartTotal'] + $shippingCosts['rawTotalShippingCost'];
                $validCoupons = [];
                $data = [];// cartItems?
                $details = $couponLayer->applyCouponBag($_orderSectionSubtotal, "afterShipping", $cartLayer->getCouponBag(), $validCoupons, $data);
                $_orderSectionTotal = $details['rawDiscountPrice'];
                $_orderGrandTotal = $_orderSectionTotal;


                $orderSectionSubtotal = E::price($_orderSectionSubtotal);
                $orderSectionTotal = E::price($_orderSectionTotal);
                $orderGrandTotal = E::price($_orderGrandTotal);

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


                $currentStep = $_SESSION['ekom.order.singleAddress']["current_step"];


                $cartModel = EkomApi::inst()->cartLayer()->getCartModel();
                $cartTotal = $cartModel['cartTotal'];
                $_cartTotal = $cartModel['rawCartTotal'];

                $_couponTotalSaving = 0;
                $_couponTotalSaving += $cartModel['rawTotalSaving'];
                $_couponTotalSaving += $details['rawTotalSaving'];

                $couponTotalSaving = E::price(-$_couponTotalSaving);


                $model = [
                    "checkoutMode" => $checkoutMode,
                    "isB2B" => $cartModel['isB2B'],
                    "billingAddress" => $billingAddress,
                    "shippingAddress" => $shippingAddress,
                    "shippingAddresses" => $shippingAddresses,
                    "selectedShippingAddressId" => $shippingAddressId,
                    "defaultCountry" => $countryId,
                    "shippingAddressFormModel" => $form->getModel(),
                    "useSingleCarrier" => $hasCarrierChoice,
                    "paymentMethodBlocks" => $paymentMethodBlocks,
                    "currentStep" => $currentStep,
                    "orderSectionSubtotal" => $orderSectionSubtotal,
                    "rawOrderSectionSubtotal" => $_orderSectionSubtotal,
                    "orderSectionTotal" => $orderSectionTotal,
                    "rawOrderSectionTotal" => $_orderSectionTotal,
                    "orderGrandTotal" => $orderGrandTotal,
                    "rawOrderGrandTotal" => $_orderGrandTotal,
                    "beforeShippingCoupons" => $cartModel['coupons'],
                    "afterShippingCouponDetails" => $details,
                    "couponTotalSaving" => $couponTotalSaving,
                    "rawCouponTotalSaving" => $_couponTotalSaving,
                    "paymentMethodId" => $paymentMethodId,
                    "paymentMethodOptions" => $paymentMethodOptions,
                    "paymentMethod" => $paymentMethod, // or null
                    "orderSections" => $shippingCosts,
                    //
                    "taxAmount" => $cartModel['taxAmount'],
                    "rawTaxAmount" => $cartModel['rawTaxAmount'],
                    "linesTotal" => $cartModel['linesTotal'],
                    "rawLinesTotal" => $cartModel['rawLinesTotal'],
                    "cartTotal" => $cartTotal,
                    "rawCartTotal" => $_cartTotal,
//                            "shippingType" => "singleAddressShipping", // singleAddressShipping|multipleAddressShipping // implicitly: it's singleAddressShipping, unless otherwise specified
                ];
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


    public function placeOrder($cleanOnSuccess = true)
    {
        try {

            $checkoutMode = E::conf("checkoutMode");
            if ('singleAddress' === $checkoutMode) {

                EkomApi::inst()->initWebContext();

                $userId = EkomApi::inst()->userLayer()->getUserId();
                $this->forReal = true;
                $model = $this->getOrderModel();
                $this->forReal = false;

                if (null === $model['paymentMethod']) {
                    throw new IncompleteOrderException("Incomplete order: missing paymentMethod");
                }

                $ret = QuickPdo::transaction(function () use ($model, $userId, $cleanOnSuccess) {

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


                    if (false !== ($orderId = EkomApi::inst()->order()->create([
                            'user_id' => $userId,
                            'reference' => EkomApi::inst()->orderLayer()->getUniqueReference(),
                            'date' => date("Y-m-d H:i:s"),
                            'tracking_number' => $model['orderSections']['sections'][0]['trackingNumber'],
                            'user_info' => serialize($userInfo),
                            'shop_info' => serialize($shopInfo),
                            'shipping_address' => serialize($invoiceAddress),
                            'billing_address' => serialize($billingAddress),
                            'order_details' => serialize($details),
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

            }

        } catch (IncompleteOrderException $e) {
            XLog::error("[Ekom module] - CheckoutLayer: $e");
        }

        return false;
    }


    public function cleanSessionOrder()
    {
        unset($_SESSION['ekom.order.singleAddress']);
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
            switch ($key) {
                case 'shipping_address_id':
                    $_SESSION['ekom.order.singleAddress']["shipping_address_id"] = $value;
                    break;
                case 'payment_method':
                    list($id, $paymentMethodOptions) = $value;
                    $_SESSION['ekom.order.singleAddress']["payment_method_id"] = $id;
                    $_SESSION['ekom.order.singleAddress']["payment_method_options"] = $paymentMethodOptions;
                    break;
                default:
                    break;
            }

            if (null !== $options) {
                if (array_key_exists("marker", $options) && null !== $options['marker']) {
                    $_SESSION['ekom.order.singleAddress']["current_step"] = $options["marker"];
                }
                if (array_key_exists("saveAsDefault", $options) && true === (bool)$options['saveAsDefault']) {
                    switch ($key) {
                        case 'shipping_address_id':
                            EkomApi::inst()->userLayer()->setPreferredShippingAddressId($value);
                            break;
                        default:
                            throw new \Exception("Unknown key: $key");
                            break;
                    }
                }
            }
        } else {
            throw new \Exception("Not implemented yet");
        }
    }

    private function initOrderModel()
    {
        SessionTool::start();

        if ('singleAddress' === E::conf("checkoutMode")) {
            if (false === array_key_exists("ekom.order.singleAddress", $_SESSION)) {
                /**
                 *
                 * class-modules/Ekom/doc/ekom-checkout-synopsis.md
                 *
                 *
                 * - ekom.order.singleAddress
                 * ----- billing_address_id
                 * ----- shipping_address_id
                 * ----- carrier_id
                 * ----- ?carrier_options array of key => value, depending on the carrier (read relevant carrier doc for more info)
                 * ----- payment_method_id
                 * ----- ?payment_method_options: array of key => value, depending on the payment method (read relevant payment method doc for more info)
                 *
                 *
                 */
                $userLayer = EkomApi::inst()->userLayer();
                $userId = $userLayer->getUserId();
                $billingAddressId = $userLayer->getUserBillingAddressId($userId);
                if (false === $billingAddressId) { // the user might not have a billing address yet
                    $billingAddressId = null;
                }

                $shippingAddressId = $userLayer->getPreferredShippingAddressId($userId);
                if (false === $shippingAddressId) { // the user might not have a shipping address yet
                    $shippingAddressId = null;
                }


                // choose default carrier if none is set
                $carrierId = null;
                if (false !== ($id = EkomApi::inst()->carrierLayer()->getDefaultCarrierId())) {
                    $carrierId = $id;
                }


                $_SESSION['ekom.order.singleAddress'] = [
                    "billing_address_id" => $billingAddressId,
                    "shipping_address_id" => $shippingAddressId,
                    "carrier_id" => $carrierId,
                    "payment_method_id" => null,
                    "payment_method_options" => null,
                    "current_step" => 0,
                ];
            }
        } else {
            throw new \Exception("Not implemented yed");
        }
    }


}