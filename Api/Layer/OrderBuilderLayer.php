<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Exception\IncompleteOrderException;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Status\Action\EkomStatusAction;
use Module\Ekom\Utils\E;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use QuickPdo\QuickPdo;


/**
 * This class might become the new OrderLayer in the future.
 *
 * See implementation notes: class-modules/Ekom/doc/implementation-notes/OrderBuilderLayer.md
 *
 *
 *
 * Quick synopsis
 * --------------------
 * $builder = EkomApi::inst()->orderBuilderLayer();
 * $builder->setBillingAddressId(1);
 * $builder->setShippingAddressId(1);
 * $builder->setPaymentMethodId(1);
 * $builder->setPaymentMethodOptions([]);
 * $builder->placeOrder();
 *
 *
 */
class OrderBuilderLayer
{

    public function __construct()
    {
    }


    public function init()
    {


        $checkoutMode = E::conf("checkoutMode");
        if ('singleAddress' === $checkoutMode) {
            if (false === EkomSession::has("order.singleAddress")) {


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
                    //
                    "_shipping_address_id" => false,
                    "_billing_address_id" => false,
                    "_carrier_id" => false,
                    "_payment_method_id" => false,
                    "_payment_method_options" => false,
                ]);
            }
        } else {
            $this->error("not implemented with checkoutMode=$checkoutMode");
        }
    }


    public function getCurrentStep()
    {
        $this->init();
        if (
            false === $_SESSION['ekom']['order.singleAddress']['_billing_address_id'] ||
            (
                true === $this->requiresShippingAddress() &&
                false === $_SESSION['ekom']['order.singleAddress']['_shipping_address_id']
            )
        ) {
            return 'address';
        } elseif (
            /**
             * If in singleAddress mode and there is only one choice, we skip that step
             */
            false === $_SESSION['ekom']['order.singleAddress']['_carrier_id'] &&
            'singleAddress' === E::conf("checkoutMode") &&
            false === EkomApi::inst()->carrierLayer()->useSingleCarrier()

        ) {
            return 'carrier';
        } elseif (
            false === $_SESSION['ekom']['order.singleAddress']['_payment_method_id'] ||
            false === $_SESSION['ekom']['order.singleAddress']['_payment_method_options']
        ) {
            return 'payment';
        }
        return "ok";
    }

    public function requiresShippingAddress()
    {
        return (0 !== (int)EkomApi::inst()->cartLayer()->getTotalWeight());
    }


    public function clean()
    {
        EkomSession::remove("order.singleAddress");
    }


    public function setShippingAddressId($id)
    {
        $this->init();
        $userId = E::getUserId();
        if (true === EkomApi::inst()->userAddressLayer()->userOwnsAddress($userId, $id)) {
            $_SESSION['ekom']['order.singleAddress']['shipping_address_id'] = (int)$id;
            $_SESSION['ekom']['order.singleAddress']['_shipping_address_id'] = true;
        } else {
            throw new EkomApiException("This user_id/address_id pair doesn't match for shipping address: $userId/$id");
        }
    }

    public function setBillingAddressId($id)
    {
        $this->init();
        $userId = E::getUserId();
        if (true === EkomApi::inst()->userAddressLayer()->userOwnsAddress($userId, $id)) {
            $_SESSION['ekom']['order.singleAddress']['billing_address_id'] = (int)$id;
            $_SESSION['ekom']['order.singleAddress']['_billing_address_id'] = true;
        } else {
            throw new EkomApiException("This user_id/address_id pair doesn't match for billing address: $userId/$id");
        }
    }

    public function setCarrierId($id)
    {
        $this->init();
        $_SESSION['ekom']['order.singleAddress']['carrier_id'] = (int)$id;
        $_SESSION['ekom']['order.singleAddress']['_carrier_id'] = true;
    }

    public function setPaymentMethodId($id)
    {
        $this->init();
        $shopId = E::getShopId();
        if (true === EkomApi::inst()->paymentLayer()->shopHasPaymentMethod($shopId, $id)) {
            $_SESSION['ekom']['order.singleAddress']['payment_method_id'] = (int)$id;
            $_SESSION['ekom']['order.singleAddress']['_payment_method_id'] = true;
        } else {
            throw new EkomApiException("This shop_id/payment_method_id pair doesn't match for payment method id address: $shopId/$id");
        }
    }

    public function setPaymentMethodOptions(array $options)
    {
        $this->init();
        $_SESSION['ekom']['order.singleAddress']['payment_method_options'] = $options;
        $_SESSION['ekom']['order.singleAddress']['_payment_method_options'] = true;
    }

    public function getOrderModel()
    {
        return EkomApi::inst()->checkoutLayer()->getOrderModel();
    }

    public function placeOrder($cleanOnSuccess = true)
    {
        $checkoutMode = E::conf("checkoutMode");
        if ('singleAddress' === $checkoutMode) {

            EkomApi::inst()->initWebContext();

            $currentStep = $this->getCurrentStep();
            if ('ok' === $currentStep) {


                $userId = EkomApi::inst()->userLayer()->getUserId();

                $this->forReal = true;
                $model = $this->getOrderModel();
                $this->forReal = false;


                // tmp
                $model['paymentMethod'] = "creditCard";


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
                            $this->clean();
                            $_SESSION['ekom']['order.last'] = $orderId;
                        }
                    }
                    return false;

                }, function (\Exception $e) {
                    XLog::error("[Ekom module] - CheckoutLayer: $e");
                });


                return $ret;

            } else {
                $this->error("Some steps need to be completed: the current step is $currentStep");
            }

        } else {
            $this->error("Not implemented with checkoutMode=$checkoutMode");
        }

        return false;
    }

    private function error($msg)
    {
        $msgLog = "[Ekom Module] - OrderBuilderLayer: $msg";
        XLog::error($msgLog);
        throw new EkomApiException($msg);
    }
}


