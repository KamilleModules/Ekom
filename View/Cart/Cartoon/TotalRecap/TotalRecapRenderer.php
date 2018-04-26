<?php


namespace Module\Ekom\View\Cart\Cartoon\TotalRecap;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class TotalRecapRenderer extends BaseRenderer
{

    private $cartModel;
    private $rows;
    private $rowsLabels;
    private $rows2Property;

    public function __construct()
    {
        parent::__construct();

        $this->cartModel = null;
        $this->rows = [
            "subtotal",
            "shippingCostWithoutTax",
            "shippingTax",
            "shippingTaxAndDetails",
            "shippingCostWithTax",
            "orderTotal",
            "coupons",
            "couponsAndDetails",
            "orderGrandTotal",
        ];
        $this->rowsLabels = [
            "subtotal" => "Sous-Total HT",
            "shippingCostWithoutTax" => "Frais de livraison HT",
            "shippingTax" => "Taxe frais de livraison",
            "shippingTaxAndDetails" => "Taxe frais de livraison",
            "shippingCostWithTax" => "Frais de livraison TTC",
            "orderTotal" => "Total avant coupons",
            "coupons" => "Coupons",
            "couponsAndDetails" => "Coupons",
            "orderGrandTotal" => "Total",
        ];
        $this->rows2Property = [
            "subtotal" => "cart_total_tax_included_formatted",
            "shippingCostWithoutTax" => "shipping_cost_tax_excluded_formatted",
            "shippingTax" => "shippingTaxAmount",
            "shippingTaxAndDetails" => "shipping_cost_tax_amount_formatted",
            "shippingCostWithTax" => "shipping_cost_tax_included_formatted",
            "orderTotal" => "order_total_formatted",
            "coupons" => "order_saving_total_formatted",
            "couponsAndDetails" => "coupons",
            "orderGrandTotal" => "order_grand_total_formatted",
        ];
    }


    public function setCartModel(array $cartModel)
    {
        $this->cartModel = $cartModel;
        return $this;
    }


    public function render()
    {
        ?>
        <table class="cartoon-rows total-recap">
            <?php foreach ($this->rows as $name): ?>
                <tr>
                    <td><?php echo $this->rowsLabels[$name]; ?></td>
                    <td><?php echo $this->cartModel[$this->rows2Property[$name]]; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }

    public function setRows($rows)
    {
        $this->rows = $rows;
        return $this;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function renderRow($name)
    {
        $overrideMethod = "renderRowOuterContent" . ucfirst($name);
        if (method_exists($this, $overrideMethod)) {
            call_user_func([$this, $overrideMethod], $name);
        } else {
            ?>
            <tr>
                <td><?php echo $this->rowsLabels[$name]; ?></td>
                <td><?php echo $this->cartModel[$this->rows2Property[$name]]; ?></td>
            </tr>
            <?php
        }
    }


    protected function renderRowOuterContentShippingTaxAndDetails($name)
    {
        ?>
        <tr class="nobottomborder">
            <td><?php echo $this->rowsLabels['shippingTaxAndDetails']; ?></td>
            <td></td>
        </tr>
        <tr class="notopborder">
            <td class="indent-1">
                <?php echo $this->cartModel['shippingTaxGroupLabel']; ?>
            </td>
            <td><?php echo $this->cartModel['shippingTaxAmount']; ?></td>
        </tr>
        <?php
    }

    protected function renderRowOuterContentCouponsTaxAndDetails($name)
    {
        ?>
        <tr class="nobottomborder">
            <td><?php echo $this->rowsLabels['coupons']; ?></td>
            <td></td>
        </tr>
        <?php
        $nbCoupons = count($this->cartModel['couponDetails']);
        $i = 1;
        foreach ($this->cartModel['couponDetails'] as $coupon):
            $noBottomBorder = ($nbCoupons === $i) ? "" : "nobottomborder";
            ?>
            <tr class="notopborder <?php echo $noBottomBorder; ?>">
                <td class="indent-1"><?php echo $coupon["label"]; ?></td>
                <td><?php echo $coupon["saving"]; ?></td>
            </tr>
            <?php
            $i++;
        endforeach; ?>
        <?php
    }

}