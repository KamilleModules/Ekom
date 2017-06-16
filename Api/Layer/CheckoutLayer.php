<?php


namespace Module\Ekom\Api\Layer;

use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;


class CheckoutLayer
{

    /**
     * @return false|array orderModel,
     *              return false if the user is not connected
     *
     */
    public function getOrderModel()
    {
        if (SessionUser::isConnected()) {


            if ('singleAddress' === E::conf("checkoutMode")) {


                $api = EkomApi::inst();

                // start collecting order data
                $this->initOrderModel();


                // taking data out of sections
                $a = $_SESSION['ekom.order.singleAddress'];
                $billingAddressId = $a["billing_address_id"];
                $shippingAddressId = $a["shipping_address_id"];
                $carrierId = $a["carrier_id"];
                $paymentMethodId = $a["payment_method_id"];
                $carrierStep = $a["current_step"];


                $userLayer = $api->userLayer();
                $carrierLayer = $api->carrierLayer();
                $couponLayer = $api->couponLayer();
                $cartLayer = $api->cartLayer();


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
                $_orderSectionTotal  = $details['rawDiscountPrice'];
                $_orderGrandTotal  = $_orderSectionTotal;



                $orderSectionSubtotal  = E::price($_orderSectionSubtotal);
                $orderSectionTotal  = E::price($_orderSectionTotal);
                $orderGrandTotal  = E::price($_orderGrandTotal);

                /**
                 * @var $provider OnTheFlyFormProviderInterface
                 */
                $provider = X::get("Core_OnTheFlyFormProvider");
                $form = $provider->getForm("Ekom", "UserAddress");
                $hasCarrierChoice = $carrierLayer->useSingleCarrier();
                $paymentMethodBlocks = $api->paymentLayer()->getShopPaymentMethodBlockModels();


                $currentStep = $_SESSION['ekom.order.singleAddress']["current_step"];



                $model = [
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
                    "orderSectionTotal" => $orderSectionTotal,
                    "orderGrandTotal" => $orderGrandTotal,
                    "afterShippingCouponDetails" => $details,
                    "orderSections" => $shippingCosts,
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


                $paymentMethodId = EkomApi::inst()->paymentLayer()->getDefaultPaymentMethodId($userId);
                if (false === $paymentMethodId) {
                    $paymentMethodId = null;
                }


                $_SESSION['ekom.order.singleAddress'] = [
                    "billing_address_id" => $billingAddressId,
                    "shipping_address_id" => $shippingAddressId,
                    "carrier_id" => $carrierId,
                    "payment_method_id" => $paymentMethodId,
                    "current_step" => 0,
                ];
            }
        } else {
            throw new \Exception("Not implemented yed");
        }
    }


}
