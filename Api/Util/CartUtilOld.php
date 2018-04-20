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

class CartUtilOld
{


    /**
     * @param array $shippingInfo
     * @return bool, whether or not the given shipping info is valid (contains no special error (form2))
     * @see EkomModels::shippingInfoModel()
     */
    public static function isValidShippingInfo($shippingInfo)
    {
        return (is_array($shippingInfo) && false === array_key_exists("errorCode", $shippingInfo));
    }


    /**
     * @param array $shippingInfo (form1)
     * @param array $cartModel
     * @return array
     * @throws \Exception
     */
    public static function getTaxInfoByValidShippingInfo(array $shippingInfo, array $cartModel)
    {
        $shippingTaxGroup = null;
        Hooks::call("Ekom_Cart_defineShippingTaxGroup", $shippingTaxGroup, $cartModel);
        if (is_string($shippingTaxGroup)) {
            $shippingTaxGroup = TaxLayer::getTaxGroupInfoByName($shippingTaxGroup);
        }
        $shippingCost = $shippingInfo['shipping_cost'];
        return TaxLayer::applyTaxGroup($shippingTaxGroup, $shippingCost);
    }

    /**
     *
     * Return whether or not the cart contains at least one item that needs to be shipped.
     * This is useful for instance for:
     * - checkout: knowing whether or not to display the shipping step
     * - cart: do we have shipping fee?
     *
     *
     * @param array $cartModel
     * @see EkomModels::cartModel()
     * @return bool
     *
     */
    public static function hasAtLeastOneShippableItem(array $cartModel)
    {
        return ($cartModel['cartTotalWeight'] > 0);
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
            $shippingDetails = $cart['shippingDetails'];
            if (array_key_exists("carrier_id", $shippingDetails)) {
                $carrierId = $shippingDetails['carrier_id'];
            }
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
                    $arr['shipping_cost_raw'] = $shippingInfo['shipping_cost'];
                    $arr['shipping_cost'] = E::price($shippingInfo['shipping_cost']);
                    $taxInfo = CartUtil::getTaxInfoByValidShippingInfo($shippingInfo, $cart);
                    $arr['shipping_cost_tax_applied_raw'] = $taxInfo['priceWithTax'];
                    $arr['shipping_cost_tax_applied'] = E::price($taxInfo['priceWithTax']);
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
        $shopAddress = self::getCurrentShopAddress($shippingAddress);
        return [
            "cartItems" => $earlyCartModel['items'],
            "cartWeight" => $earlyCartModel['cart_total_weight'],
            "shippingAddress" => $shippingAddress,
            "storeAddress" => $shopAddress,
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


    public static function generateTokenByProductIdMajorProductDetails($productId, array $majorDetails = [])
    {
        $token = (string)$productId;

        if (count($majorDetails) > 0) {
            ksort($majorDetails);
            $sDetails = implode('-', $majorDetails); // this will be enough for now, later we could use a hash
            $token .= '-' . $sDetails;
        }
        return $token;
    }


    /**
     * @param array $items
     * @return array of seller_name => info, with info having the following structure:
     *
     * - has_tax: bool, Whether at least one item had the tax applied to it
     * - total: the sum of the items sale_price
     * - total_formatted
     * - tax_amount_total: the total amount of tax collected for this seller
     * - tax_amount_total_formatted
     * - taxDetails: an array, each entry representing a tax group applied to at least one product for this seller.
     *              Each entry is an array of taxGroupName to item, each item being an array with the following structure:
     *              - taxGroupLabel: string, the tax group label
     *              - taxAmountTotalRaw: number, the cumulated amount coming from this tax group for this seller
     *              - taxAmountTotal: the formatted version of taxAmountTotalRaw
     *
     * - items: the items for the current seller
     *
     * @throws \Exception
     *
     */
    public static function getItemsGroupedBySeller(array $items)
    {
        $ret = [];

        foreach ($items as $item) {

            $seller = $item['seller'];


            if (false === array_key_exists($seller, $ret)) {


                $ret[$seller] = [
                    'label' => [],
                    /**
                     * taxHint is a number indicating
                     * the type of visual hint to display next to the price totals
                     * for every seller.
                     *
                     * - 0: none
                     * - 1: no tax (HT in french)
                     * - 2: with tax (TTC in french)
                     *
                     */
                    'taxHint' => 0,
                    'total' => 0,
                    'cartWeight' => 0,
                    'taxAmountTotalRaw' => 0,
                    'taxAmountTotal' => 0,
                    'totalRaw' => 0,
                    'taxDetails' => [],
                    'items' => [],
                ];
            }

            if (!array_key_exists($item['taxGroupName'], $ret[$seller]['taxDetails'])) {
                $ret[$seller]['taxDetails'][$item['taxGroupName']] = [
                    'taxGroupLabel' => $item['taxGroupLabel'],
                    'taxAmountTotalRaw' => 0,
                    'taxAmountTotal' => 0,
                ];
            }


            $ret[$seller]['taxDetails'][$item['taxGroupName']]['taxAmountTotalRaw'] += $item['taxAmount'];
            $ret[$seller]['taxAmountTotalRaw'] += $item['taxAmount'];
            $ret[$seller]['totalRaw'] += $item['priceLineRaw'];
            $ret[$seller]['cartWeight'] += $item['weight'] * $item['quantityCart'];
            $ret[$seller]['items'][] = $item;
        }


        foreach ($ret as $seller => $item) {
            $ret[$seller]['label'] = $item['items'][0]['seller_label'];
            $ret[$seller]['total'] = E::price($item['totalRaw']);
            $ret[$seller]['taxAmountTotal'] = E::price($item['taxAmountTotalRaw']);

            $taxDetails = $ret[$seller]['taxDetails'];
            if ($taxDetails) {
                foreach ($taxDetails as $k => $v) {
                    $v['taxAmountTotal'] = E::price($v['taxAmountTotalRaw']);
                    $taxDetails[$k] = $v;
                }
                $ret[$seller]['taxDetails'] = $taxDetails;
            }

            $taxHint = 0;
            Hooks::call("Ekom_Cart_getSellerTaxHint", $taxHint, $seller, $ret[$seller]["items"]);
            $ret[$seller]['taxHint'] = $taxHint;
        }
        return $ret;
    }


    /**
     * This method returns one cart per seller.
     *
     * This is used in some paymentMethodHandlers, which need to have a hand on the repayment schedules
     * for every seller!
     *
     */
    public static function getSellerCarts(array $cartModel)
    {
        $itemsBySeller = $cartModel['itemsGroupedBySeller'];
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

        $totalWeight = $cartModel['cartTotalWeight'];
        $nbShippingParticipants = 0;
        $couponDetails = $cartModel['couponDetails'];
        $nbSellers = count($itemsBySeller);
        if ($nbSellers) {
            $sellerCouponRatio = 1 / $nbSellers;
        } else {
            $sellerCouponRatio = 1;
        }


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
         * Effectively creating the carts
         */
        $allCarts = [];
        $taxGroupName = $cartModel['shippingTaxGroupName'];
        $shippingDetails = $cartModel['shippingDetails'];
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


                $shippingCostTotal = $cartModel['shippingShippingCostWithoutTaxRaw'];
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
                 * The shipping info might not be available (if the user is not connected for instance)
                 */
                if ($shippingDetails) {
                    $shippingInfo = [
                        "estimated_delivery_date" => $shippingDetails['estimated_delivery_date'],
                        "shipping_cost" => $sellerShippingCost,
                    ];
                } else {
                    $shippingInfo = [
                        "estimated_delivery_date" => null,
                        "shipping_cost" => 0,
                    ];
                    $shippingDetails["carrier_id"] = null;
                    $shippingDetails["label"] = null;
                }
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


            $sellerCartModel = $entity->getModel();
            $allCarts[$seller] = $sellerCartModel;
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
    private static function getCurrentShopAddress(array $shippingAddress = null)
    {
        /**
         * If the shop address was already SELECTED (by ekom), then use this address
         */
        $addressId = CurrentCheckoutData::getShopAddressId();
        if (null !== $addressId) {
            return ShopLayer::getPhysicalAddressById($addressId);
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