<?php


namespace Module\Ekom\Utils\CheckoutOrder;


use ArrayToString\ArrayToStringTool;
use Bat\ArrayTool;
use Core\Services\Hooks;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Api\Layer\UserGroupLayer;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Api\Util\CartUtil;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Status\EkomOrderStatus;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\OrderReferenceProvider\OrderReferenceProviderInterface;
use QuickPdo\QuickPdo;

class CheckoutOrderUtilOld
{

    private $testMode;


    public function __construct()
    {
        $this->testMode = false;
    }


    public static function create()
    {
        return new static();
    }

    public function setTestMode($testMode)
    {
        $this->testMode = $testMode;
        return $this;
    }


    /**
     * This method places an order in the database and returns the id of the order.
     * It
     *
     *
     * orderData
     * -------------
     * @param array $data
     * - user_id
     * - shop_id
     * - lang_id
     * - currency_id
     * - ?carrier_id              (only if at least an item needs to be shipped)
     * - ?shipping_address_id     (only if at least an item needs to be shipped)
     * - ?shop_address_id         (only if the shipping_address_id is defined)
     * - billing_address_id
     * - payment_method_id
     * - ...your own
     *
     *
     * @param array $cartModel
     * @see EkomModels::cartModel()
     * @return int, the id of the order
     *
     * @throws EkomUserMessageException
     * @throws \Exception
     */
    public function placeOrder(array $data, array $cartModel)
    {
        /**
         * 1. check (and hooks)
         * 2. collect (&) (and hooks)
         * 3. db insert
         */
        //--------------------------------------------
        // CHECK
        //--------------------------------------------
        /**
         * Very first checking is about errorCode.
         * errorCode can come from carrier (shippingErrorCode) or paymentMethodHandler.
         *
         * An errorCode prevents the checkout process to complete,
         * and so we handle them asap.
         *
         * An errorCode translates to a public message being sent
         * to the user when she clicks the pay button.
         *
         * The team can acknowledge the will from the user to pay and
         * recontact the user later.
         *
         * A typical carrier errorCode translated into text looks like this (to give you ideas of how errorCode is used):
         *
         * - Dear customer, your order address is shipped to a location that we don't usually deliver.
         * Our team will choose the most appropriate shipping method for your order and recontact you by phone
         * as soon as possible.
         *
         *
         *
         * Otherwise, (if no errorCode)
         * We check that we have all the data we want,
         * and it's also time to check the consistency of the data
         * (is the user really who she/he pretends to be, does she/he own
         * the address, etc...).
         *
         * A check error will throw an exception.
         * Then, we ask the modules to do the same (checking the consistency of the data they have collected,
         * which should be in the CCD that we pass to them, at least that's our recommendation,
         * see my notes:
         *
         * @see SokoPaymentCheckoutProcessStep::getModel()
         *
         *
         */
        $shippingErrorCode = $cartModel['shippingErrorCode'];
        if (null !== $shippingErrorCode) {
            self::handleShippingErrorCode($shippingErrorCode);
        }


        $importantKeys = [
            'user_id',
            'shop_id',
            'lang_id',
            'currency_id',
            'billing_address_id',
            'payment_method_id',
        ];
        if (true === CartUtil::hasAtLeastOneShippableItem($cartModel)) {
            $importantKeys = array_merge($importantKeys, [
                'carrier_id',
                'shipping_address_id',
                'shop_address_id',
            ]);
        }
        $missing = ArrayTool::getMissingKeys($data, $importantKeys);
        if (false === $missing) {

            $this->checkDataConsistency($data, $cartModel);

            //--------------------------------------------
            // COLLECT
            //--------------------------------------------
            /**
             * In this step, we collect the information to save in the database.
             * We want to recreate the orderModel -- @see EkomModels::orderModel()
             *
             * This involves two special actors:
             * - carriers                   (CarrierInterface)
             * - payment method handlers    (PaymentMethodHandlerInterface)
             *
             * Those actors potentially will fetch data using external apis
             * (for instance, carrier can ask for a tracking number, and payment method handler
             * will execute a financial transaction with a bank...).
             *
             *
             * This is a synchronous process anyway (i.e. all statements are executed in order),
             * so, at the end of this step, we end up with the order model that we insert in database in next step.
             *
             * Modules are prompted to decorate this model with their own data.
             * In particular, this process will listen to (and handle) the following entries:
             *
             * - pay_identifier: string, an identifier to track your financial transaction
             * - tracking_number: string
             * - payment_method_details: array (will be injected in orderModel.order_details.payment_method_details),
             *                          contain info like does the user want to use a repayment schedule or not
             *                          for instance
             * - carrier_details: array (will be injected in orderModel.order_details.carrier_details)
             *                          contain info like do we want to send the item as a gift for instance (i.e. the
             *                          store needs to wrap the item) for instance
             *
             *
             *
             */


            $orderModel = [];
            /**
             * @var $refProvider OrderReferenceProviderInterface
             */
            $refProvider = X::get("Ekom_getOrderReferenceProvider");
            $reference = $refProvider->getReference();
            /**
             * The reference might be needed by some payment handlers (for tracking financial transactions,
             * hence we provide it right away).
             */
            $orderModel['reference'] = $reference;


            //  we start with special objects
            //--------------------------------------------
            $shopId = $data['shop_id'];
            $langId = $data['lang_id'];
            $billingAddressId = $data['billing_address_id'];

            // carrier
            $carrierId = null;
            if (array_key_exists("carrier_id", $data)) {
                $carrierId = $data['carrier_id'];
                $carrier = CarrierLayer::getCarrierInstanceById($carrierId, $shopId);
                $carrier->placeOrder($orderModel, $cartModel, $data);
            }


            // payment method handler
            $paymentMethodId = $data['payment_method_id'];
            $paymentHandler = PaymentLayer::getPaymentMethodHandlerById($paymentMethodId);
            $paymentHandler->placeOrder($orderModel, $cartModel, $data);


            // now ekom logic
            $payIdentifier = (array_key_exists("pay_identifier", $orderModel)) ? $orderModel['pay_identifier'] : "";
            $trackingNumber = (array_key_exists("tracking_number", $orderModel)) ? $orderModel['tracking_number'] : "";
            $paymentMethodDetails = (array_key_exists("payment_method_details", $orderModel)) ? $orderModel['payment_method_details'] : [];
            $carrierDetails = (array_key_exists("carrier_details", $orderModel)) ? $orderModel['carrier_details'] : [];

            $userId = (int)$data['user_id'];
            $userInfo = UserLayer::getUserInfoById($userId);
            if (false === $userInfo) { // I'm a little paranoid here, since this has been already checked, but it shouldn't hurt
                $this->devError("See, I was not too paranoid: user not found: $userId");
            }
            $userGroups = UserGroupLayer::getUserGroups($userId);
            $userInfo['groups'] = implode(",", $userGroups);


            $shopInfo = ShopLayer::getShopInfoById($shopId);
            if (false === $shopInfo) {
                $this->devError("Yikes: shop info not found: $shopId");
            }

            $shopAddressId = (array_key_exists("shop_address_id", $data)) ? $data['shop_address_id'] : null;
            if (null !== $shopAddressId) {
                $shopAddress = ShopLayer::getPhysicalAddressById($shopAddressId, $shopId, $langId);
            } else {
                $shopAddress = ShopLayer::getDefaultShopAddress();
            }
            $shopInfo['address'] = $shopAddress;

            $shippingAddressId = (array_key_exists("shipping_address_id", $data)) ? $data['shipping_address_id'] : null;
            if (null !== $shippingAddressId) {
                $shippingAddress = UserAddressLayer::getAddressById($userId, $shippingAddressId, $langId);
            } else {
                $shippingAddress = false;
            }
            $billingAddress = UserAddressLayer::getAddressById($userId, $billingAddressId, $langId);


            $_cartModel = $cartModel;
            $paymentMethodName =PaymentLayer::getPaymentMethodNameById($paymentMethodId);
            if(false === $paymentMethodName){
                $this->devError("Inconsistent data: payment method name not found with id $paymentMethodId");
            }
            $orderDetails = [
                "cartModel" => $_cartModel,
                "payment_method_id" => $paymentMethodId,
                "payment_method_name" => $paymentMethodName,
                "payment_method_details" => $paymentMethodDetails,
            ];
            if (null !== $carrierId) { // did we use a carrier for this order?
                $orderDetails = array_replace($orderDetails, [
                    "carrier_id" => $carrierId,
                    "carrier_name" => $carrier->getName(),
                    "carrier_details" => $carrierDetails,
                ]);
            }


            //--------------------------------------------
            // LAST OPPORTUNITY FOR MODULES TO DECORATE ORDER DETAILS
            //--------------------------------------------
            Hooks::call("Ekom_CheckoutOrderUtil_decorateOrderDetails", $orderDetails, $data);


            // ?
            unset($orderDetails['cartModel']['itemsGroupedBySeller']);


            // I let this model in non serialized form for debugging
            $orderModel = array_replace($orderModel, [
                "user_id" => $userId,
                "date" => date('Y-m-d H:i:s'),
                "amount" => $cartModel['priceOrderGrandTotalRaw'],
                "currency" => E::getCurrencyIso(),
                "pay_identifier" => $payIdentifier,
                "tracking_number" => $trackingNumber,
                "user_info" => $userInfo,
                "shop_info" => $shopInfo,
                "shipping_address" => $shippingAddress,
                "billing_address" => $billingAddress,
                "order_details" => $orderDetails,
            ]);


            //--------------------------------------------
            // INSERT IN DATABASE
            //--------------------------------------------
            return $this->placeOrderModel($orderModel, $shopId);


        } else {
            $this->devError("The following keys are missing: " . implode(", ", $missing));
        }

    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function userError($msg)
    {
        throw new EkomException($msg);
    }

    private function devError($msg)
    {
        throw new EkomUserMessageException($msg);
    }

    /**
     * No data missing, checking consistency.
     *
     * - the user belongs to the shop
     * - the user owns the billing address
     * - if the shipping step is involved:
     *      - the user owns the shipping address
     *      - the carrier belongs to the shop
     *      - the shop address really belongs to the shop
     * - the shop owns the payment method id
     *
     */
    private function checkDataConsistency(array $data, $cartModel)
    {
        $userId = (int)$data['user_id'];
        $shopId = (int)$data['shop_id'];
        $billingAddressId = (int)$data['billing_address_id'];
        $paymentMethodId = (int)$data['payment_method_id'];


        // the user belongs to the shop
        $res = QuickPdo::fetch("select id from ek_user where id=$userId and shop_id=$shopId");
        if (false === $res) {
            $this->devError("Inconsistent data: the user $userId does not belong to the shop $shopId");
        }

        // the user owns the billing address
        $res = QuickPdo::fetch("select user_id from ek_user_has_address where user_id=$userId and address_id=$billingAddressId");
        if (false === $res) {
            $this->devError("Inconsistent data: the user $userId does not own the billing address $billingAddressId");
        }


        // do we use shipping step?
        if (array_key_exists("carrier_id", $data)) {
            $carrierId = (int)$data['carrier_id'];
            $shippingAddressId = (int)$data['shipping_address_id'];
            $shopAddressId = (int)$data['shop_address_id'];

            // the user owns the shipping address
            $res = QuickPdo::fetch("select user_id from ek_user_has_address where user_id=$userId and address_id=$shippingAddressId");
            if (false === $res) {
                $this->devError("Inconsistent data: the user $userId does not own the shipping address $shippingAddressId");
            }

            // the carrier belongs to the shop
            $res = QuickPdo::fetch("select shop_id from ek_shop_has_carrier where shop_id=$shopId and carrier_id=$carrierId");
            if (false === $res) {
                $this->devError("Inconsistent data: the carrier $carrierId does not belong to the shop $shopId");
            }

            // the shop address really belongs to the shop
            $res = QuickPdo::fetch("select id from ek_shop_has_address where shop_id=$shopId and address_id=$shopAddressId");
            if (false === $res) {
                $this->devError("Inconsistent data: the shop address $shopAddressId does not belong to the shop $shopId");
            }
        }


        // the shop owns the payment method id
        $res = QuickPdo::fetch("select shop_id from ek_shop_has_payment_method where shop_id=$shopId and payment_method_id=$paymentMethodId");
        if (false === $res) {
            $this->devError("Inconsistent data: the shop $shopId does not own the payment method $paymentMethodId");
        }


        //--------------------------------------------
        // ASKING MODULES
        //--------------------------------------------
        try {
            Hooks::call("Ekom_CheckoutOrderUtil_checkDataConsistency", $data, $cartModel);
        } catch (\Exception $e) {
            $this->devError($e->getMessage());
        }

    }


    private function placeOrderModel(array $orderModel, $shopId)
    {
        $orderId = null;
        $exception = null;
        QuickPdo::transaction(function () use ($orderModel, $shopId, &$orderId) {


            $_orderId = EkomApi::inst()->order()->create([
                'user_id' => $orderModel['user_id'],
                'reference' => $orderModel['reference'],
                'date' => $orderModel['date'],
                'amount' => $orderModel['amount'],
                'currency' => $orderModel['currency'],
                'pay_identifier' => (string)$orderModel['pay_identifier'],
                'tracking_number' => $orderModel['tracking_number'],
                'user_info' => serialize($orderModel['user_info']),
                'shop_info' => serialize($orderModel['shop_info']),
                'shipping_address' => serialize($orderModel['shipping_address']),
                'billing_address' => serialize($orderModel['billing_address']),
                'order_details' => serialize($orderModel['order_details']),
            ]);

            if (false === $_orderId) {
                $this->devError("Could not insert the order model: " . ArrayToStringTool::toPhpArray($orderModel));
            }


            $orderId = $_orderId;
            OrderLayer::addOrderStatusByCode($orderId, EkomOrderStatus::STATUS_PAYMENT_SENT, $shopId);
            Hooks::call("Ekom_CheckoutOrderUtil_onPlaceOrderSuccessAfter", $orderId, $orderModel);


            if (false === $this->testMode) {
                CartLayer::create()->clean();
                CurrentCheckoutData::clean();
            } else {
                az("test mode", $orderModel);
            }


        }, function (\Exception $e) {
            $this->devError("$e");
        });
        return $orderId;
    }


    private static function handleShippingErrorCode($errorCode)
    {
        $appErrorInfo = [
            'text' => null,
            'notificationType' => 'error',
        ];
        Hooks::call("Ekom_CheckoutOrderUtil_handleShippingErrorCode", $appErrorInfo, $errorCode);

        $appErrorText = $appErrorInfo['text'];
        if (null === $appErrorText) {
            $appErrorText = "The carrier has interrupted the checkout process with errorCode $errorCode. 
            Please contact our team for more info.";
        }
        throw EkomUserMessageException::create($appErrorText)->setType($appErrorInfo['notificationType']);
    }
}