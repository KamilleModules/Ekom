<?php


namespace Module\Ekom\Api\Util;


use Core\Services\Hooks;
use Core\Services\X;
use Kamille\Services\Exception\HooksException;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Layer\CarrierLayer;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Api\Layer\UserAddressLayer;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\DistanceEstimator\DistanceEstimatorInterface;
use Module\Ekom\Utils\E;
use Module\ThisApp\ThisAppConfig;

class CartUtil
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
     * @throws HooksException
     *
     */
    public static function getCarrierOffers($shopId = null, $langId = null)
    {
        $carrierOffers = [];
        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        $context = CartUtil::getCarrierShippingInfoContext(CartLayer::create()->getCartModel(), $shopId, $langId);
        $cart = CartLayer::create()->getCartModel();

        $carrierId = CurrentCheckoutData::getCarrierId();
        if (null === $carrierId) {
            $shippingDetails = $cart['shippingDetails'];
            if (array_key_exists("carrier_id", $shippingDetails)) {
                $carrierId = $shippingDetails['carrier_id'];
            }
        }
        $carrierId = (int)$carrierId;


        $carriers = CarrierLayer::getCarrierInstancesByShop($shopId);
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
                XLog::error("[Ekom module] - CartUtil.getCarrierOffers: why does this shippingInfo call fail? carrierId: $id, shopId: $shopId, langId: $langId");
            }
        }
        return $carrierOffers;
    }


    /**
     * @see EkomModels::shippingContextModel()
     */
    public static function getCarrierShippingInfoContext(array $primitiveCartModel, $shopId = null, $langId = null)
    {


        $shopId = E::getShopId($shopId);
        $langId = E::getLangId($langId);

        /**
         * Can the carrier calculate the shippingInfo?
         */
        $shippingAddress = self::getCurrentShippingAddress($langId);
        $shopAddress = self::getCurrentShopAddress($shopId, $langId, $shippingAddress);
        return [
            "cartItems" => $primitiveCartModel['items'],
            "cartWeight" => $primitiveCartModel['cartTotalWeight'],
            "shippingAddress" => $shippingAddress,
            "shopAddress" => $shopAddress,
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
     * @return array of seller => info, with info having the following structure:
     *
     * - taxHint: int, a number indicating
     *                  the type of visual hint to display next to the price totals for every seller.
     *                  Whether or not the tax was globally applied.
     *
     * - total: the total to display
     * - totalRaw: the internal total used for computation
     * - taxAmountTotal: the total amount of tax for this seller
     * - taxAmountTotalRaw: the internal total of tax for this seller
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
    public static function orderItemsBySeller(array $items)
    {
        $ret = [];

        foreach ($items as $item) {

            $seller = $item['seller'];

            if (false === array_key_exists($seller, $ret)) {


                $ret[$seller] = [
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
    private static function getCurrentShippingAddress($langId = null)
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
            return UserAddressLayer::getAddressById($userId, $addressId, $langId);
        }

        /**
         * Otherwise, does the user have a preferred address?
         */
        return UserAddressLayer::getPreferredShippingAddress($userId, $langId);
    }


    /**
     * Return the shop address.
     *
     * @return null|array:shopPhysicalAddress
     * @see EkomModels::shopPhysicalAddress()
     * @throws EkomException
     */
    private static function getCurrentShopAddress($shopId, $langId, array $shippingAddress = null)
    {
        /**
         * If the shop address was already SELECTED (by ekom), then use this address
         */
        $addressId = CurrentCheckoutData::getShopAddressId();
        if (null !== $addressId) {
            return ShopLayer::getPhysicalAddressById($addressId, $shopId, $langId);
        }

        /**
         * Otherwise, we return the warehouse address closest to the user's shipping address.
         * Note: that does apply only if the shop has multiple warehouse addresses.
         */
        $addresses = ShopLayer::getPhysicalAddresses(null, $shopId, $langId);
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