<?php


namespace Module\Ekom\Api\Util;


use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Services\Exception\HooksException;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Entity\CartModelEntity;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\StoreLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\Checkout\CheckoutUtil;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\DistanceEstimator\DistanceEstimatorInterface;
use Module\Ekom\Utils\E;
use Module\ThisApp\ThisAppConfig;

class CartUtil
{
    /**
     * Return
     * @param $estimatedDeliveryDate
     * @return string
     */
    public static function getEstimatedDeliveryDate($estimatedDeliveryDate)
    {
        if (null !== $estimatedDeliveryDate) {
            if (is_string($estimatedDeliveryDate)) {
                return E::localys()->getLongDate(strtotime($estimatedDeliveryDate));
            } elseif (is_array($estimatedDeliveryDate)) {
                list($start, $end) = $estimatedDeliveryDate;
                list($start, $end) = E::localys()->getLongDateRangeBits(strtotime($start), strtotime($end));
                return "Entre le $start et le $end";
            } else {
                return "Non disponible";
            }
        } else {
            return "Aucune";
        }
    }

    /**
     * @param array $shippingInfo
     * @return bool, whether or not the given shipping info is valid (contains no special error (form2))
     * @see EkomModels::shippingInfoModel()
     *
     *
     *
     * - ?estimated_delivery_text:
     * - ?estimated_delivery_date: null|datetime|[datetimeStart, datetimeEnd]
     * - shipping_cost: number, the cost of the shipping of the accepted products (without tax applied)
     */
    public static function isValidShippingInfo(array $shippingInfo)
    {

        return (
            array_key_exists("shipping_cost", $shippingInfo) &&
            (
                array_key_exists("estimated_delivery_text", $shippingInfo) ||
                array_key_exists("estimated_delivery_date", $shippingInfo)
            )
        );
    }


    /**
     * @param array $earlyCartModel , the cart model in construction before shipping tax are applied.
     * @see CartLayer::doGetCartModel() for more info.
     * @return array
     * @throws HooksException
     */
    public static function getShippingCostTaxInfoByEarlyCartModel(array $earlyCartModel)
    {
        $taxAmount = 0;
        $taxLabel = "";
        $taxName = "";


        $shippingTaxName = null;
        Hooks::call("Ekom_Cart_defineShippingTaxName", $shippingTaxName, $earlyCartModel);
        if (is_string($shippingTaxName)) {
            $taxInfo = TaxLayer::getTaxInfoByName($shippingTaxName);
            if (false !== $taxInfo) {
                $taxLabel = $taxInfo['label'];
                $taxAmount = $taxInfo['amount'];
                $taxName = $shippingTaxName;
            } else {
                XLog::error("tax with name $shippingTaxName not found in the database");
            }
        }
        return [
            "tax_amount" => $taxAmount,
            "tax_label" => $taxLabel,
            "tax_name" => $taxName,
        ];
    }

    /**
     *
     * Return whether or not the cart contains at least one item that needs to be shipped.
     * This is useful for instance for:
     * - checkout: knowing whether or not to display the shipping step
     * - cart: do we have shipping fee?
     *
     *
     * @param array $extendedCartModel
     * @see EkomModels::extendedCartModel()
     * @return bool
     *
     */
    public static function hasAtLeastOneShippableItem(array $extendedCartModel)
    {
        return ($extendedCartModel['cart']['cart_total_weight'] > 0);
    }

    /**
     * @param null $shopId
     * @param null $langId
     * @return array of <carrierOfferModel>
     * @see EkomModels::carrierOfferModel()
     * @throws \Exception
     *
     */
    public static function getCarrierOffers()
    {
        $carrierOffers = [];

        $cartModel = CheckoutUtil::getCurrentCartLayer()->getCartModel();

        $context = CartUtil::getCarrierShippingInfoContext($cartModel);
        $cart = $cartModel;

        $carrierId = CurrentCheckoutData::getCarrierId();
        if (null === $carrierId) {
            $carrierId = $cart['carrier_id'];
        }
        $carrierId = (int)$carrierId;


        $carriers = CarrierLayer::getCarrierInstances();
        foreach ($carriers as $id => $carrier) {
            $shippingInfo = $carrier->getShippingInfo($context);
            if (false !== $shippingInfo) {

                $arr = $shippingInfo;

                $arr['name'] = $carrier->getName();
                $arr['label'] = $carrier->getLabel();
                $arr['selected'] = (int)$id === $carrierId;
                if (true === CartUtil::isValidShippingInfo($shippingInfo)) {

                    $shippingCostTaxExcluded = $shippingInfo['shipping_cost'];

                    $arr['shipping_cost_tax_excluded'] = $shippingCostTaxExcluded;
                    $arr['shipping_cost_tax_excluded_formatted'] = E::price($shippingCostTaxExcluded);


                    $taxInfo = CartUtil::getShippingCostTaxInfoByEarlyCartModel($cart);
                    $shippingCostTaxAmount = $taxInfo['tax_amount'];
                    $shippingCostTaxIncluded = $shippingCostTaxExcluded + ($shippingCostTaxExcluded * $shippingCostTaxAmount / 100);

                    $arr['shipping_cost_tax_included'] = $shippingCostTaxIncluded;
                    $arr['shipping_cost_tax_included_formatted'] = E::price($shippingCostTaxIncluded);
                }
                ksort($arr);
                $carrierOffers[$id] = $arr;
            } else {
                XLog::error("[Ekom module] - CartUtil.getCarrierOffers: why does this shippingInfo call fail? carrierId: $id");
            }
        }
        return $carrierOffers;
    }


    /**
     * @see EkomModels::shippingContextModel()
     */
    public static function getCarrierShippingInfoContext(array $earlyCartModel)
    {
        /**
         * Can the carrier calculate the shippingInfo?
         */
        $shippingAddress = self::getCurrentShippingAddress();
        $storeAddress = self::getCurrentStoreAddress($shippingAddress);
        return [
            "cartItems" => $earlyCartModel['items'],
            "cartWeight" => $earlyCartModel['cart_total_weight'],
            "shippingAddress" => $shippingAddress,
            "storeAddress" => $storeAddress,
        ];
    }


    /**
     * Return the shopAddress (from the given shopAddresses) closest to the given shipping address.
     * If shopAddresses is empty, null is returned.
     *
     *
     * @param $shippingAddress array:addressModel
     * @see EkomModels::addressModel()
     *
     * @param $shopAddresses array of shopPhysicalAddress
     * @see EkomModels::shopPhysicalAddress()
     *
     *
     * @return array|null
     */
    public static function getClosestPhysicalAddress(array $shippingAddress, array $shopAddresses)
    {
        /**
         * @var $estimator DistanceEstimatorInterface
         */
        $estimator = X::get("Ekom_DistanceEstimator");
        $closest = null;
        $distance = 30000; // impossible to reach distance
        foreach ($shopAddresses as $shopAddress) {
            $distanceToUserCountry = $estimator->estimate($shopAddress, $shippingAddress);
            if ($distanceToUserCountry < $distance) {
                $distance = $distanceToUserCountry;
                $closest = $shopAddress;
            }
        }
        return $closest;
    }


    public static function generateTokenByProductReferenceId($productReferenceId, array $minorDetails = [])
    {
        $token = (string)$productReferenceId;

        if (count($minorDetails) > 0) {
            throw new \Exception("Not implemented yet");
            ksort($majorDetails);
            $sDetails = implode('-', $majorDetails); // this will be enough for now, later we could use a hash
            $token .= '-' . $sDetails;
        }
        return $token;
    }


    /**
     * @see EkomModels::extendedCartModel()
     */
    public static function getExtendedCartModel()
    {
        return CheckoutUtil::getCurrentCartLayer()->getExtendedCartModel();
    }


    /**
     * @return CartLayer
     */
    public static function getCart()
    {
        return CheckoutUtil::getCurrentCartLayer();
    }

    /**
     * @param array $items
     * @return array of items grouped by sellers:
     * @see EkomModels::itemsGroupedBySeller()
     *
     * @throws \Exception
     *
     */
    public static function getItemsGroupedBySeller(array $items)
    {
        $ret = [];

        foreach ($items as $item) {

            $sellerName = $item['seller_name'];

            if (false === array_key_exists($sellerName, $ret)) {
                $taxDetails = [];
                $lineTaxDetails = $item['line_tax_details'];
                foreach ($lineTaxDetails as $taxLabel => $lineTaxAmount) {
                    $taxDetails[$taxLabel] = [
                        "tax_amount" => $lineTaxAmount,
                        "tax_amount_formatted" => E::price($lineTaxAmount),
                    ];
                }
                $ret[$sellerName] = [
                    'label' => $item['seller_label'],
                    'has_tax' => false, // has at least one item with tax
                    'total_weight' => 0,
                    'total' => 0,
                    'total_formatted' => "0",
                    'total_tax_amount' => 0,
                    'total_tax_amount_formatted' => "0",
                    'tax_details' => $taxDetails,
                    'items' => [],
                ];
            } else {
                $lineTaxDetails = $item['line_tax_details'];
                foreach ($lineTaxDetails as $taxLabel => $lineTaxAmount) {
                    $ret[$sellerName]["tax_details"][$taxLabel]['tax_amount'] += $lineTaxAmount;
                    $ret[$sellerName]["tax_details"][$taxLabel]['tax_amount_formatted'] = E::price($lineTaxAmount);
                }
            }


            if (true === $item['has_tax']) {
                $ret[$sellerName]['has_tax'] = true;
            }


            $ret[$sellerName]['total_tax_amount'] += ($item['line_sale_price'] - $item['line_base_price']);
            $ret[$sellerName]['total'] += $item['line_sale_price'];
            $ret[$sellerName]['total_weight'] += $item['weight'] * $item['cart_quantity'];

            $ret[$sellerName]['items'][] = $item;
        }


        foreach ($ret as $seller => $item) {
            $ret[$seller]['total_formatted'] = E::price($item['total']);
            $ret[$seller]['total_tax_amount_formatted'] = E::price($item['total_tax_amount']);
        }


        return $ret;
    }


    /**
     * This method returns one cart per seller.
     *
     * This is used in some paymentMethodHandlers, which need to have a hand on the repayment schedules
     * for every seller!
     *
     * @param $options :
     *      - onItemAfter
     *
     *
     */
    public static function getSellerCarts(array $extendedCartModel, array $options = [])
    {

        $onItemAfter = $options['onItemAfter'] ?? null;


        $itemsBySeller = $extendedCartModel['itemsGroupedBySeller'];
        $cartModel = $extendedCartModel['cart'];
        /**
         * Pre-loop
         * -------------
         * Fetching important info for the process loop below.
         * In other words, we collect directives in the first loop, and the second loop
         * is dedicated to apply directives, effectively creating the cart(s).
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

        $totalWeight = $cartModel['cart_total_weight'];
        $nbShippingParticipants = 0;
        $couponDetails = $cartModel['coupons_details'];
        $nbSellers = count($itemsBySeller);
        if ($nbSellers) {
            $sellerCouponRatio = 1 / $nbSellers;
        } else {
            $sellerCouponRatio = 1;
        }


        foreach ($itemsBySeller as $seller => $item) {

            $participateToShipping = $item['total_weight'] > 0;

            if (true === $participateToShipping) {
                $shippingRatio = $item['total_weight'] / $totalWeight;
                $nbShippingParticipants++;
            } else {
                $shippingRatio = 0;
            }


            $sellerCouponDetails = [];
            foreach ($couponDetails as $couponDetailsItem) {
                $sellerName = $couponDetailsItem['seller_name'];

                if (null === $sellerName) {
                    $amount = $couponDetailsItem['amount'];
                    $couponDetailsItem['amount'] = $amount * $sellerCouponRatio;
                    $couponDetailsItem['amount_formatted'] = E::price($couponDetailsItem['amount']);
                    $couponDetailsItem['details']['sellerDetails'] = "saving x sellerRatio = $amount x $sellerCouponRatio";
                    $sellerCouponDetails[] = $couponDetailsItem;
                } else {
                    if ($seller === $sellerName) {
                        $amount = $couponDetailsItem['amount'];
                        $couponDetailsItem['details']['sellerDetails'] = "100% of $amount = $amount";
                        $sellerCouponDetails[] = $couponDetailsItem;
                    }
                }
            }


            $sellerInfo[$seller] = [
                'shippingRatio' => $shippingRatio,
                /**
                 * participate to the shipping costs?
                 */
                'useShipping' => $item['total_weight'] > 0,
                'couponDetails' => $sellerCouponDetails,
            ];

        }


        /**
         * Process loop
         * --------------
         * Effectively creating the carts
         */
        $allCarts = [];
        $shippingTaxName = $cartModel['shipping_cost_tax_name'];
        $carrierId = $cartModel['carrier_id'];
        $carrierLabel = $cartModel['carrier_label'];


        $currentShippingCostPaid = 0;

        foreach ($itemsBySeller as $seller => $item) {
            $nbShippingParticipants--;
            $sellerDirectives = $sellerInfo[$seller];


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


//                $shippingCostTotalExcluded = $cartModel['shipping_cost_tax_excluded'];
                $shippingCostTotalIncluded = $cartModel['shipping_cost_tax_included'];
//                $sellerShippingCost = $shippingCostTotalExcluded * $percent;
                $sellerShippingCost = $shippingCostTotalIncluded * $percent;
                $currentShippingCostPaid += $sellerShippingCost;

                /**
                 * For the last participant (to shipping cost),
                 * we need to round up the price, so that the sum of
                 * participants contribution matches EXACTLY the total shipping cost
                 *
                 */
                if (0 === $nbShippingParticipants) {
//                    $sellerShippingCost += ($shippingCostTotalExcluded - $currentShippingCostPaid);
                    $sellerShippingCost += ($shippingCostTotalIncluded - $currentShippingCostPaid);
                }

                /**
                 * @see EkomModels::shippingInfoModel()
                 * The shipping info might not be available (if the user is not connected for instance)
                 */


                if (4 === $cartModel['shipping_status']) {
                    $shippingInfo = [
                        "estimated_delivery_date" => $cartModel['carrier_estimated_delivery_date'],
                        "shipping_cost" => $sellerShippingCost,
                    ];
                } else {
                    $shippingInfo = [
                        "estimated_delivery_date" => null,
                        "shipping_cost" => 0,
                    ];
                    $carrierId = null;
                    $carrierLabel = null;
                }


                $entity->setShippingData($shippingInfo,
                    $shippingTaxName,
                    $carrierId,
                    $carrierLabel,
                    $cartModel['carrier_error_code'],
                    $cartModel['shipping_status']
                );
            }


            //--------------------------------------------
            // COUPONS
            //--------------------------------------------
            if ($sellerDirectives['couponDetails']) {
                $sellerCouponDetails = $sellerDirectives['couponDetails'];
                $entity->addCouponDetails($sellerCouponDetails);
            }


            $sellerCartModel = $entity->getModel();
            $allCarts[$seller] = $sellerCartModel;


            if ($onItemAfter) {
                call_user_func($onItemAfter, $seller, $item, $sellerCartModel);
            }
        }


        return $allCarts;
    }
    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Return the shipping address of the connected user for the checkout process,
     * or null is such an address doesn't exist yet.
     *
     * @return null|array:addressModel
     * @see EkomModels::addressModel()
     * @throws EkomException
     */
    private static function getCurrentShippingAddress()
    {
        /**
         * If the user is not connected and/or disconnect,
         * then we temporarily "cancel" the current shipping address
         */
        if (false === E::userIsConnected()) {
            return null;
        }


        /**
         * The address SELECTED by the user during the checkout process
         * has precedence.
         */
        $userId = E::getUserId();
        $addressId = CurrentCheckoutData::getShippingAddressId();
        if (null !== $addressId) {
            /**
             * Might be the case that the user was on the checkout page and
             * just deleted the current address.
             * In this case, we need to resort to another address
             */
            try {

                return UserAddressLayer::getAddressById($userId, $addressId);
            } catch (\Exception $e) {
                // well, the last statement at the bottom of this method seems to do the trick
            }
        }

        /**
         * Otherwise, does the user have a preferred address?
         */
        return UserAddressLayer::getPreferredShippingAddress($userId);
    }


    /**
     * Return the shop address.
     *
     * @return null|array:shopPhysicalAddress
     * @see EkomModels::shopPhysicalAddress()
     * @throws EkomException
     */
    private static function getCurrentStoreAddress(array $shippingAddress = null)
    {
        /**
         * If the shop address was already SELECTED (by ekom), then use this address
         */
        $addressId = CurrentCheckoutData::getStoreAddressId();
        if (null !== $addressId) {
            return StoreLayer::getPhysicalAddressById($addressId);
        }


        /**
         * Otherwise, we return the warehouse address closest to the user's shipping address.
         * Note: that does apply only if the shop has multiple warehouse addresses.
         */
        $addresses = StoreLayer::getPhysicalAddresses();
        if ($addresses) {
            if (null !== $shippingAddress && count($addresses) > 1) { // multiple addresses, we choose the closest
                return CartUtil::getClosestPhysicalAddress($shippingAddress, $addresses);
            }

            // only one address, that's our default
            return array_shift($addresses);
        }
        return null;
    }
}