<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\InvoiceInfo;


use Kamille\Services\XConfig;
use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class OrderInvoiceInfoRenderer extends BaseRenderer
{

    /**
     * @see EkomModels::orderModel()
     */
    private $order;

    public function __construct()
    {
        parent::__construct();

        $this->columns = [
            'invoice_info_date',
            'invoice_info_number',
            'invoice_info_number_alt',
            'invoice_info_order_amount',
            'invoice_info_order_payment_method',
            'invoice_info_currency',
        ];
    }


    public function setOrder(array $order)
    {
        $this->order = $order;
        return $this;
    }


    public function render()
    {
        $orderDetails = $this->order['order_details'];
        $paymentMethod = $orderDetails['payment_method_name'];
        $cartModel = $orderDetails['cartModel']['cart'];
        $paymentDetails = $orderDetails['payment_method_details'];
        ?>
        <div class="invoice-info">
            <table class="cartoon-rows">
                <?php if ($this->has('invoice_info_date')): ?>
                    <tr>
                        <td>Date</td>
                        <td><?php echo substr($this->order['date'], 0 ,10); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($this->has('invoice_info_number')): ?>
                    <tr>
                        <td>N° de bon de commande:</td>
                        <td><?php echo $this->order['reference']; ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($this->has('invoice_info_order_amount')): ?>
                    <tr>
                        <td>Montant total:</td>
                        <td><?php echo $cartModel['order_grand_total_formatted']; ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($this->has('invoice_info_order_payment_method')): ?>
                    <tr>
                        <td>Méthode de paiement:</td>
                        <td>
                            <?php if ('credit_card_wallet' === $paymentMethod):
                                $cardLastFourDigits = $paymentDetails['credit_card']['last_four_digits']; ?>
                                Carte bleue se terminant par <?php echo $cardLastFourDigits; ?>
                            <?php elseif ('transfer' === $paymentMethod): ?>
                                Virement
                            <?php else: ?>
                                Inconnu
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($this->has('invoice_info_currency')): ?>
                    <tr>
                        <td>Devise:</td>
                        <td><?php echo XConfig::get("Ekom.currencyIsoCode"); ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
        <?php
    }

    //--------------------------------------------
    //
    //--------------------------------------------


}