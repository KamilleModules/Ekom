<?php


namespace Module\Ekom\Api\Entity;

use Bat\UriTool;
use Kamille\Services\XLog;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Api\Util\CartUtil;
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

    // shipping data
    private $shippingInfo;
    private $shippingTaxName;
    private $shippingStatus;
    private $carrierId;
    private $carrierLabel;
    private $carrierErrorCode;


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
     * @see EkomModels::shippingInfoModel()
     * @return $this
     */
    public function setShippingData(array $shippingInfo, $shippingTaxName, $carrierId, $carrierLabel, $carrierErrorCode, $shippingStatus)
    {
        $this->shippingInfo = $shippingInfo;
        $this->shippingTaxName = $shippingTaxName;
        $this->shippingStatus = $shippingStatus;
        $this->carrierId = $carrierId;
        $this->carrierLabel = $carrierLabel;
        $this->carrierErrorCode = $carrierErrorCode;
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
            $model['cart_total_weight'] = $this->totalWeight;
            $model['cart_tax_amount'] = $this->cartTaxAmount;
            $model['cart_total_tax_included'] = $this->cartTotal;
            $model['cart_total_tax_excluded'] = $this->cartTotal - $this->cartTaxAmount;
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
        $orderTotal = $model['cart_total_tax_included'] + $model["shipping_cost_tax_included"];
        $model["order_total"] = $orderTotal;


        $this->applyCouponDetails($model);


        //--------------------------------------------
        // ROUND UP
        //--------------------------------------------
        /**
         * Note: this allows modules to deal only with raw values (in case they change the cart model)
         */
        $model['cart_tax_amount_formatted'] = E::price($model['cart_tax_amount']);
        $model['cart_total_tax_included_formatted'] = E::price($model['cart_total_tax_included']);
        $model['cart_total_tax_excluded_formatted'] = E::price($model['cart_total_tax_excluded']);
        $model['coupons_total_formatted'] = E::price($model['coupons_total']);

        // order
        $model["shipping_cost_tax_included_formatted"] = E::price($model["shipping_cost_tax_included"]);
        $model["shipping_cost_tax_excluded_formatted"] = E::price($model["shipping_cost_tax_excluded"]);
        $model["shipping_cost_tax_amount_formatted"] = E::price($model["shipping_cost_tax_amount"]);
        $model["order_total_formatted"] = E::price($model["order_total"]);
        $model["order_grand_total_formatted"] = E::price($model["order_grand_total"]);


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

                $cartQuantity = $boxModel['cart_quantity'];

                //--------------------------------------------
                // updating total weight and quantities
                //--------------------------------------------
                $weight = $boxModel['weight'];
                $totalQty += $cartQuantity;
                $totalWeight += $weight * $cartQuantity;


                //--------------------------------------------
                // extending the box model to
                //--------------------------------------------
                $productDetails = $boxModel['selected_product_details'];
//                $uriDetails = UriTool::uri($boxModel['uriProduct'], $productDetails, true);
                $uriDetails = $boxModel['product_uri_with_details'];


                $linePrice = E::trimPrice($cartQuantity * $boxModel['sale_price']);
                $cartTotal += $linePrice;

                $linePriceWithoutTax = E::trimPrice($cartQuantity * $boxModel['base_price']);
                $cartTotalWithoutTax += $linePriceWithoutTax;


//                $taxAmount = $boxModel['sale_price'] - $boxModel['base_price'];
//                $itemTaxAmount = E::trimPrice($cartQuantity * $taxAmount);
                $lineTaxAmount = $boxModel['line_tax_amount'];
                $cartTaxAmount += $lineTaxAmount;


                $boxModel['uri_card_with_details'] = $uriDetails;
                $boxModel['line_sale_price'] = $linePrice;
                $boxModel['line_sale_price_formatted'] = E::price($linePrice);
                $boxModel['line_base_price'] = $linePriceWithoutTax;
                $boxModel['line_base_price_formatted'] = E::price($linePriceWithoutTax);
                $boxModel['line_tax_amount'] = $lineTaxAmount;


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
        $shippingTaxName = $this->shippingTaxName;
        $carrierEstimatedDeliveryDate = CartUtil::getEstimatedDeliveryDate($shippingInfo['estimated_delivery_date']);

        $model['carrier_id'] = $this->carrierId;
        $model['carrier_label'] = $this->carrierLabel;
        $model['carrier_estimated_delivery_date'] = $carrierEstimatedDeliveryDate;


        $shippingTaxInfo = TaxLayer::getTaxInfoByName($shippingTaxName);
        $shippingCostTaxAmount = $shippingTaxInfo['amount'];
        $shippingCostTaxExcluded = E::trimPrice($shippingInfo['shipping_cost']);
        $shippingCostTaxIncluded = $shippingCostTaxExcluded + ($shippingCostTaxExcluded * $shippingCostTaxAmount / 100);


        $model['carrier_error_code'] = $this->carrierErrorCode;
        $model['shipping_status'] = $this->shippingStatus;

        $model['shipping_cost_tax_excluded'] = $shippingCostTaxExcluded;
        $model['shipping_cost_tax_excluded_formatted'] = E::price($shippingCostTaxExcluded);
        $model['shipping_cost_tax_included'] = $shippingCostTaxIncluded;
        $model['shipping_cost_tax_included_formatted'] = E::price($shippingCostTaxIncluded);
        $model['shipping_cost_discount_amount'] = 0; // not handled yet, @todo-ling?
        $model['shipping_cost_discount_amount_formatted'] = E::price(0);

        $model['shipping_cost_tax_amount'] = $shippingCostTaxAmount;
        $model['shipping_cost_tax_amount_formatted'] = E::price($shippingCostTaxAmount);

        $model['shipping_cost_tax_label'] = $shippingTaxInfo['label'];
        $model['shipping_cost_tax_name'] = $shippingTaxInfo["name"];
    }


    private function applyCouponDetails(array &$model)
    {
        $orderGrandTotal = $model["order_total"];
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

        $model['order_grand_total'] = $orderGrandTotal;
        $model['coupons'] = $this->couponsDetails;
        $model['has_coupons'] = (count($this->couponsDetails) > 0);
        $model['coupons_total'] = $model["order_total"] - $orderGrandTotal;
    }
}

