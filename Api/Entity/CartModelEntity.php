<?php


namespace Module\Ekom\Api\Entity;

use Bat\UriTool;
use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\E;


/**
 *
 * Helps recreating a cartModel.
 *
 *
 * @see EkomModels::cartModel()
 *
 */
class CartModelEntity
{

    protected $items;
    private $totalQty;
    private $totalWeight;
    private $cartTotal;
    private $cartTotalWithoutTax;
    private $cartTaxAmount;

    // shipping item
    private $shippingInfo;
    private $taxGroupName;
    private $carrierId;
    private $carrierLabel;


    // coupons
    private $couponsDetails;
    private $_primitiveModel;


    public function __construct()
    {
        $this->items = [];
        $this->shippingInfo = null;
        $this->couponsDetails = [];
        $this->_primitiveModel = null;
    }

    public static function create()
    {
        return new static();
    }


    public function addProduct(array $boxModel)
    {
        $this->items[] = $boxModel;
        $this->_primitiveModel = null;
        return $this;
    }


    /**
     *
     * @param array $shippingInfo
     * @see EkomModels::shippingInfoModel()
     *
     * @param $taxGroupName
     * @param $carrierId
     * @param $carrierLabel
     * @return $this
     */
    public function addShippingItem(array $shippingInfo, $taxGroupName, $carrierId, $carrierLabel)
    {
        $this->shippingInfo = $shippingInfo;
        $this->taxGroupName = $taxGroupName;
        $this->carrierId = $carrierId;
        $this->carrierLabel = $carrierLabel;
        return $this;
    }


    /**
     *
     * @param array $couponDetails , array of <couponDetailsItem>
     * @see EkomModels::couponDetailsItem()
     * @return $this
     */
    public function addCouponDetails(array $couponDetails)
    {
        $this->couponsDetails = $couponDetails;
        return $this;
    }


    public function getPrimitiveModel()
    {
        if (null === $this->_primitiveModel) {

            // base cart
            $this->preCart();
            $model = [];
            $model['items'] = $this->items;
            $model['cart_total_quantity'] = $this->totalQty;
            $model['cartTotalWeight'] = $this->totalWeight;
            $model['cartTaxAmountRaw'] = $this->cartTaxAmount;
            $model['priceCartTotalRaw'] = $this->cartTotal;
            $model['priceCartTotalWithoutTaxRaw'] = $this->cartTotal - $this->cartTaxAmount;
            $this->_primitiveModel = $model;
        }
        return $this->_primitiveModel;
    }


    public function getModel()
    {

        $model = $this->getPrimitiveModel();

        // shipping info
        $this->applyShippingItem($model);


        // order total
        $orderTotal = $model['priceCartTotalRaw'] + $model["shippingShippingCostRaw"];
        $model["priceOrderTotalRaw"] = $orderTotal;


        $this->applyCouponDetails($model);


        //--------------------------------------------
        // ROUND UP
        //--------------------------------------------
        /**
         * Note: this allows modules to deal only with raw values (in case they change the cart model)
         */
        $model['cartTaxAmount'] = E::price($model['cartTaxAmountRaw']);
        $model['priceCartTotal'] = E::price($model['priceCartTotalRaw']);
        $model['priceCartTotalWithoutTax'] = E::price($model['priceCartTotalWithoutTaxRaw']);
        $model['couponSaving'] = E::price($model['couponSavingRaw']);

        // order
        $model["shippingShippingCost"] = E::price($model["shippingShippingCostRaw"]);
        $model["shippingTaxAmount"] = E::price($model["shippingTaxAmountRaw"]);
        $model["shippingShippingCostWithoutTax"] = E::price($model["shippingShippingCostWithoutTaxRaw"]);
        $model["priceOrderTotal"] = E::price($model["priceOrderTotalRaw"]);
        $model["priceOrderGrandTotal"] = E::price($model["priceOrderGrandTotalRaw"]);


        ksort($model);
        return $model;

    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function preCart()
    {
        //--------------------------------------------
        // BOXES
        //--------------------------------------------
        $totalQty = 0;
        $totalWeight = 0;
        $cartTotal = 0;
        $cartTotalWithoutTax = 0;
        $cartTaxAmount = 0;

        foreach ($this->items as $k => $boxModel) {

            if (false === array_key_exists('errorCode', $boxModel)) {

                $cartQuantity = $boxModel['quantityCart'];

                //--------------------------------------------
                // updating total weight and quantities
                //--------------------------------------------
                $weight = $boxModel['weight'];
                $totalQty += $cartQuantity;
                $totalWeight += $weight * $cartQuantity;


                //--------------------------------------------
                // extending the box model to
                //--------------------------------------------
                $productDetails = $boxModel['productDetailsMap'];
                $uriDetails = UriTool::uri($boxModel['uriProduct'], $productDetails, true);


                $linePrice = E::trimPrice($cartQuantity * $boxModel['priceSaleRaw']);
                $cartTotal += $linePrice;

                $linePriceWithoutTax = E::trimPrice($cartQuantity * $boxModel['priceBaseRaw']);
                $cartTotalWithoutTax += $linePriceWithoutTax;

                $itemTaxAmount = E::trimPrice($cartQuantity * $boxModel['taxAmount']);
                $cartTaxAmount += $itemTaxAmount;


                $boxModel['uri_card_with_details'] = $uriDetails;
                $boxModel['priceLineRaw'] = $linePrice;
                $boxModel['priceLine'] = E::price($linePrice);
                $boxModel['priceLineWithoutTaxRaw'] = $linePriceWithoutTax;
                $boxModel['priceLineWithoutTax'] = E::price($linePriceWithoutTax);
                $boxModel['taxAmount'] = $itemTaxAmount;


                ksort($boxModel);
                $this->items[$k] = $boxModel;
            } else {
                XLog::error("[Ekom module] - CartModelEntity.getModel: errorCode: " . $boxModel['errorCode'] . ", msg: " . $boxModel['errorMessage']);
            }
        }

        $this->totalQty = $totalQty;
        $this->totalWeight = round($totalWeight, 2);
        $this->cartTotal = $cartTotal;
        $this->cartTotalWithoutTax = $cartTotalWithoutTax;
        $this->cartTaxAmount = $cartTaxAmount;
    }


    private function applyShippingItem(array &$model)
    {
        $shippingInfo = $this->shippingInfo;
        if (is_string($this->taxGroupName) && is_array($shippingInfo) && false === array_key_exists("errorCode", $shippingInfo)) {

            // applying shipping taxes
            //--------------------------------------------
            $shippingCost = $shippingInfo['shipping_cost'];
            $shippingTaxGroup = TaxLayer::getTaxGroupInfoByName($this->taxGroupName);
            $taxInfo = TaxLayer::applyTaxGroup($shippingTaxGroup, $shippingCost);


            $shippingCostWithTax = E::trimPrice($taxInfo['priceWithTax']);
            $shippingCost = $taxInfo['priceWithoutTax'];

            $model["shippingTaxDetails"] = $taxInfo['taxDetails'];
            $model["shippingTaxRatio"] = $taxInfo['taxRatio'];
            $model["shippingTaxGroupName"] = $taxInfo['taxGroupName'];
            $model["shippingTaxGroupLabel"] = $taxInfo['taxGroupLabel'];
            $model["shippingTaxAmountRaw"] = $taxInfo['taxAmountUnit'];
            $model["shippingTaxHasTax"] = ($taxInfo['taxAmountUnit'] > 0); // whether or not the tax was applied
            $model["shippingDetails"] = [
                "estimated_delivery_text" => (array_key_exists("estimated_delivery_text", $shippingInfo)) ? $shippingInfo["estimated_delivery_text"] : "",
                "estimated_delivery_date" => $shippingInfo["estimated_delivery_date"],
                "label" => $this->carrierLabel,
//                "shop_address" => $shopAddress, // not sure?
                "carrier_id" => $this->carrierId,
            ];
            $model["shippingShippingCostRaw"] = $shippingCostWithTax;
            $model["shippingShippingCostWithoutTaxRaw"] = $shippingCost;
            $model["shippingIsApplied"] = true;
            $model['shippingErrorCode'] = null;
        } else {
            $shippingCostWithTax = 0;
            $model["shippingTaxDetails"] = [];
            $model["shippingTaxRatio"] = 1;
            $model["shippingTaxGroupName"] = "";
            $model["shippingTaxGroupLabel"] = "";
            $model["shippingTaxAmountRaw"] = 0;
            $model["shippingTaxHasTax"] = false;
            $model["shippingDetails"] = [];
            $model["shippingShippingCostRaw"] = $shippingCostWithTax;
            $model["shippingShippingCostWithoutTaxRaw"] = $shippingCostWithTax;
            $model["shippingIsApplied"] = false;

            if (is_array($shippingInfo) && array_key_exists("errorCode", $shippingInfo)) {
                $model['shippingErrorCode'] = $shippingInfo['errorCode'];
            } else {
                $model['shippingErrorCode'] = null;
            }
        }
    }


    private function applyCouponDetails(array &$model)
    {
        $orderGrandTotal = $model["priceOrderTotalRaw"];
        if ($this->couponsDetails) {
            /**
             * Note: although present in the array,
             * the seller_target isn't handled at this level,
             * it must be taken cared of at a higher level.
             *
             * This class primary goal basically display an invoice.
             */
            foreach ($this->couponsDetails as $couponsDetail) {
                $orderGrandTotal -= $couponsDetail['savingRaw'];
            }
        }
        if ($orderGrandTotal < 0) {
            $orderGrandTotal = 0;
        }
        $model['priceOrderGrandTotalRaw'] = $orderGrandTotal;


        $model['couponDetails'] = $this->couponsDetails;
        $model['couponHasCoupons'] = (count($this->couponsDetails) > 0);
        $model['couponSavingRaw'] = $model["priceOrderTotalRaw"] - $orderGrandTotal;
    }
}

