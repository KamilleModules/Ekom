<?php


namespace Module\Ekom\Utils\CheckoutOrder;


use ArrayToString\ArrayToStringTool;
use Bat\ArrayTool;
use Core\Services\Hooks;
use Core\Services\X;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\CartModelEntity;
use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\InvoiceLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\PaymentLayer;
use Module\Ekom\Api\Layer\SellerLayer;
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
use Module\Ekom\Utils\InvoiceNumberProvider\InvoiceNumberProviderInterface;
use Module\Ekom\Utils\OrderReferenceProvider\OrderReferenceProviderInterface;
use QuickPdo\QuickPdo;



class CheckoutOrderUtil
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
     * This method places an order and the attached invoices in the database and returns the id of the order.
     *
     * Pre-requisites:
     * https://github.com/KamilleModules/Ekom/tree/master/doc/checkout/order-invoices-payments.md
     * https://github.com/KamilleModules/Ekom/tree/master/doc/checkout/carrier-and-sellers.md
     *
     *
     *
     * @param array $data : <orderDataModel>
     * @see EkomModels::orderDataModel()
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
         *      3a. create order
         *      3b. create invoice(s)
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
             *
             *
             * We want to recreate the orderModel -- @see EkomModels::orderModel()
             *
             * This involves two special actors:
             * - carriers                   (CarrierInterface)
             * - payment method handlers    (PaymentMethodHandlerInterface)
             *
             *
             * Those actors can potentially fetch data using external apis
             * (for instance, carrier can ask for a tracking number, and payment method handler
             * will execute a financial transaction with a bank...).
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
            /**
             * The carrier is applied at the order level
             * https://github.com/KamilleModules/Ekom/tree/master/doc/checkout/carrier-and-sellers.md
             */
            $carrierId = null;
            if (array_key_exists("carrier_id", $data)) {
                $carrierId = $data['carrier_id'];
                $carrier = CarrierLayer::getCarrierInstanceById($carrierId, $shopId);
                $carrier->placeOrder($orderModel, $cartModel, $data);
            }


            // payment method handler
            /**
             * For payment method handlers, in this collect step, we only collect the user configuration
             * (of the chosen payment method) when he clicked the "pay" button.
             */
            $paymentMethodId = $data['payment_method_id'];
            $paymentHandler = PaymentLayer::getPaymentMethodHandlerById($paymentMethodId);
            $paymentMethodDetails = $paymentHandler->getCommittedConfiguration($data, $cartModel);


            // now ekom logic
            $trackingNumber = (array_key_exists("tracking_number", $orderModel)) ? $orderModel['tracking_number'] : "";
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
            $paymentMethodName = PaymentLayer::getPaymentMethodNameById($paymentMethodId);
            if (false === $paymentMethodName) {
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
//            unset($orderDetails['cartModel']['itemsGroupedBySeller']);


            $currencyIsoCode = CurrencyLayer::getIsoCodeById($data['currency_id']);
            $langIsoCode = LangLayer::getIsoCodeById($data['lang_id']);

            if (false === $currencyIsoCode) {
                $this->devError("Inconsistent data: currency " . $data['currency_id'] . " was not found");
            }

            if (false === $langIsoCode) {
                $this->devError("Inconsistent data: lang " . $data['lang_id'] . " was not found");
            }


            // I let this model in non serialized form for debugging
            $orderModel = array_replace($orderModel, [
                "shop_id" => $shopId,
                "user_id" => $userId,
                "date" => date('Y-m-d H:i:s'),
                "amount" => $cartModel['priceOrderGrandTotalRaw'],
                "currency_iso_code" => $currencyIsoCode,
                "lang_iso_code" => $langIsoCode,
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
    /**
     * @param array $orderModel
     * @return array: <invoiceModel:insert>
     * @see EkomModels::invoiceModel()
     * @throws \Exception
     */
    protected function createInvoices($orderId, array $orderModel)
    {
        $orderDetails = $orderModel['order_details'];
        $orderCartModel = $orderModel['order_details']['cartModel'];


        $itemsBySeller = $orderCartModel['itemsGroupedBySeller'];

        // by default, we create one invoice per seller
        $userId = $orderModel['user_id'];
        $shopId = $orderModel['shop_id'];
        $type = "invoice";
        $date = date("Y-m-d H:i:s");

        /**
         * @var $invoiceNumberProvider InvoiceNumberProviderInterface
         */
        $invoiceNumberProvider = X::get("Ekom_getInvoiceNumberProvider");
        $currencyIsoCode = $orderModel['currency_iso_code'];
        $langIsoCode = $orderModel['lang_iso_code'];
        $langId = LangLayer::getLangIdByIso($langIsoCode);
        $shopItem = ShopLayer::getShopItemById($orderModel['shop_id']);
        $shopHost = $shopItem['host'];
        $userInfo = $orderModel['user_info'];
        $shippingAddress = $orderModel['shipping_address'];
        $billingAddress = $orderModel['billing_address'];


        /**
         * Pre-loop
         * -------------
         * Fetching important info for the process loop below.
         * In other words, we collect directives in the first loop, and the second loop
         * is dedicated to apply directives, effectively creating invoice(s).
         * Note: some directives could create more than one invoice, or at least that's the idea.
         *
         *
         * ### Carrier
         * In current ekom, we agreed that there was only one carrier per order, and that the
         * carrier was set at the order level.
         * https://github.com/KamilleModules/Ekom/tree/master/doc/checkout/carrier-and-sellers.md
         *
         * So the only question left is: how much share of the total shipping cost each seller
         * is going to pay.
         *
         * Here, we say that if the cart weight is more than 0, then the seller is willing to participate
         * to the shipping costs, otherwise it is not.
         *
         * Now amongst the ones sharing the shipping cost, we use a proportional system where every seller
         * is given a ratio (called seller_shipping_ratio), which represents the percentage of the weight
         * handled by the seller compared to the total weight of the order.
         *
         * For instance, if we have the following:
         *
         * - seller A: 700 kg
         * - seller B: 300 kg
         * --------------------
         * - Total: 1000kg
         *
         * Then the seller A ratio is 0.7 (70%), and the seller B ratio is 0.3 (30%).
         *
         *
         *
         * ### Coupon
         *
         * We appy a similar mechanism for coupons.
         * We leverage the target of the <couponDetailsItem> -- @see EkomModels::couponDetailsItem()
         * to implement our heuristics.
         *
         *
         *
         */
        $sellerInfo = [];

        $totalWeight = $orderCartModel['cartTotalWeight'];
        $nbShippingParticipants = 0;
        $couponAmount = $orderCartModel['couponSavingRaw'];
        $couponDetails = $orderCartModel['couponDetails'];
        $nbSellers = count($itemsBySeller);
        $sellerCouponRatio = 1 / $nbSellers;


        foreach ($itemsBySeller as $seller => $item) {

            $participateToShipping = $item['cartWeight'] > 0;

            if (true === $participateToShipping) {
                $shippingRatio = $item['cartWeight'] / $totalWeight;
                $nbShippingParticipants++;
            } else {
                $shippingRatio = 0;
            }


            $sellerCouponDetails = [];
            foreach ($couponDetails as $couponDetailsItem) {
                $target = $couponDetailsItem['target'];
                if ('' === trim($target)) {
                    $savingRaw = $couponDetailsItem['savingRaw'];
                    $couponDetailsItem['savingRaw'] = $savingRaw * $sellerCouponRatio;
                    $couponDetailsItem['saving'] = E::price($couponDetailsItem['savingRaw']);
                    $couponDetailsItem['details']['sellerDetails'] = "saving x sellerRatio = $savingRaw x $sellerCouponRatio";
                    $sellerCouponDetails[] = $couponDetailsItem;
                } elseif (0 === strpos($target, "seller:")) {
                    $p = explode(":", $target, 2);
                    $sellName = trim($p[1]);
                    if ($seller === $sellName) {
                        $savingRaw = $couponDetailsItem['savingRaw'];
                        $couponDetailsItem['details']['sellerDetails'] = "100% of $savingRaw = $savingRaw";
                        $sellerCouponDetails[] = $couponDetailsItem;
                    }
                }
            }


            $sellerInfo[$seller] = [
                'shippingRatio' => $shippingRatio,
                /**
                 * participate to the shipping costs?
                 */
                'useShipping' => $item['cartWeight'] > 0,
                'couponDetails' => $sellerCouponDetails,
            ];

        }


        /**
         * Process loop
         * --------------
         * Effectively creating the invoices
         */
        $invoices = [];
        $taxGroupName = $orderCartModel['shippingTaxGroupName'];
        $shippingDetails = $orderCartModel['shippingDetails'];
        $currentShippingCostPaid = 0;

        foreach ($itemsBySeller as $seller => $item) {
            $nbShippingParticipants--;
            $sellerDirectives = $sellerInfo[$seller];


            $sellerAddress = SellerLayer::getDefaultSellerAddressByName($seller, $shopId, $langId);
            $sellerId = SellerLayer::getIdByName($seller, $shopId);

            // base properties
            $invoice = [];
            $invoice['shop_id'] = $shopId;
            $invoice['user_id'] = $userId;
            $invoice['order_id'] = $orderId;
            $invoice['seller_id'] = $sellerId;
            $invoice['label'] = "";
            $invoice['invoice_number'] = $invoiceNumberProvider->getNumber($type);
            $invoice['invoice_number_alt'] = "";
            $invoice['invoice_date'] = $date;
            $invoice['pay_identifier'] = ""; //  will be set later
            $invoice['currency_iso_code'] = $currencyIsoCode;
            $invoice['lang_iso_code'] = $langIsoCode;
            $invoice['shop_host'] = $shopHost;


            $invoice['seller'] = $seller;
            $invoice['user_info'] = $userInfo;
            $invoice['seller_address'] = $sellerAddress;
            $invoice['shipping_address'] = $shippingAddress;
            $invoice['billing_address'] = $billingAddress;


            // cart related
            /**
             * we recreate a cartModel in noGroups form.
             * @see EkomModels::cartModel()
             */
            $entity = CartModelEntity::create();
            foreach ($item['items'] as $boxModel) {
                $entity->addProduct($boxModel);
            }

            //--------------------------------------------
            // SHIPPING
            //--------------------------------------------
            /**
             * Does the shipping cost apply?
             * How?
             * ---------
             */
            if (true === $sellerDirectives['useShipping']) {
                $percent = $sellerDirectives['shippingRatio'];


                $shippingCostTotal = $orderCartModel['shippingShippingCostWithoutTaxRaw'];
                $sellerShippingCost = $shippingCostTotal * $percent;
                $currentShippingCostPaid += $sellerShippingCost;

                /**
                 * For the last participant (to shipping cost),
                 * we need to round up the price, so that the sum of
                 * participants contribution matches EXACTLY the total shipping cost
                 *
                 */
                if (0 === $nbShippingParticipants) {
                    $sellerShippingCost += ($shippingCostTotal - $currentShippingCostPaid);
                }

                /**
                 * @see EkomModels::shippingInfoModel()
                 */
                $shippingInfo = [
                    "estimated_delivery_date" => $shippingDetails['estimated_delivery_date'],
                    "shipping_cost" => $sellerShippingCost,
                ];


                $entity->addShippingItem($shippingInfo,
                    $taxGroupName,
                    $shippingDetails['carrier_id'],
                    $shippingDetails['label']
                );
            }


            //--------------------------------------------
            // COUPONS
            //--------------------------------------------
            if ($sellerDirectives['couponDetails']) {
                $sellerCouponDetails = $sellerDirectives['couponDetails'];
                $entity->addCouponDetails($sellerCouponDetails);
            }


            $cartModel = $entity->getModel();


            $invoiceDetails = $orderDetails;
            $invoiceDetails['cartModel'] = $cartModel;
            $invoice['invoice_details'] = $invoiceDetails;
            $invoice['amount'] = $cartModel['priceOrderGrandTotalRaw'];


            $invoices[$seller] = $invoice;
        }

        return $invoices;
    }

    protected function processInvoice(array $invoice, array $orderModel)
    {
            $invoiceId = InvoiceLayer::insert($invoice);

            if (false === $invoiceId) {
                $this->devError("Invoice couldn't be inserted: data= " . ArrayToStringTool::toPhpArray($invoice));
            }

    }

    //--------------------------------------------
    //
    //--------------------------------------------

    private function placeOrderModel(array $orderModel, $shopId)
    {
        $orderId = null;
        $exception = null;
        QuickPdo::transaction(function () use ($orderModel, $shopId, &$orderId) {


            $_orderId = EkomApi::inst()->order()->create([
                'shop_id' => $orderModel['shop_id'],
                'user_id' => $orderModel['user_id'],
                'reference' => $orderModel['reference'],
                'date' => $orderModel['date'],
                'amount' => $orderModel['amount'],
                'currency_iso_code' => $orderModel['currency_iso_code'],
                'lang_iso_code' => $orderModel['lang_iso_code'],
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


            //--------------------------------------------
            // NOW INVOICES...
            //--------------------------------------------
            $invoices = $this->createInvoices($orderId, $orderModel);
            foreach ($invoices as $invoice) {
                $this->processInvoice($invoice, $orderModel);
            }


            az("oo");
            //--------------------------------------------
            // FINALIZING THE ORDER PROCESS
            //--------------------------------------------
//            OrderLayer::addOrderStatusByCode($orderId, EkomOrderStatus::STATUS_PAYMENT_SENT, $shopId);
//            Hooks::call("Ekom_CheckoutOrderUtil_onPlaceOrderSuccessAfter", $orderId, $orderModel);
//
//
//            if (false === $this->testMode) {
//                CartLayer::create()->clean();
//                CurrentCheckoutData::clean();
//            } else {
//                az("test mode", $orderModel);
//            }


        }, function (\Exception $e) {
            $this->devError("$e");
        });
        return $orderId;
    }

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