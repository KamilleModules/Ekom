<?php


namespace Module\Ekom\View\Cart\Cartoon\TotalRecap;


class WithPresetsTotalRecapRenderer extends TotalRecapRenderer
{
    public function __construct()
    {
        parent::__construct();
    }


    public function setPreset($preset = null)
    {
        if (null === $preset) {
            $preset = "default";
        }
        switch ($preset) {
            case "default":
                $this->setRows([
                    "subtotal",
//            "shippingCostWithoutTax",
//            "shippingTax",
//            "shippingTaxAndDetails",
                    "shippingCostWithTax",
                    "orderTotal",
                    "coupons",
//            "couponsAndDetails",
                    "orderGrandTotal",
                ]);
                break;
            default:
                break;
        }
        return $this;
    }


}