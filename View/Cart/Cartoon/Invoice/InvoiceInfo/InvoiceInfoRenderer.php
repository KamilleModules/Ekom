<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\InvoiceInfo;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class InvoiceInfoRenderer extends BaseRenderer
{

    /**
     * @see EkomModels::invoiceModel()
     */
    private $invoice;

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


    public function setInvoice(array $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }


    public function render()
    {
        $invoiceDetails = $this->invoice['invoice_details'];
        $paymentMethod = $invoiceDetails['payment_method_name'];
        $cartModel = $invoiceDetails['cartModel'];
        $paymentDetails = $invoiceDetails['payment_method_details'];
        ?>
        <div class="invoice-info">
            <table class="cartoon-rows">
                <?php if ($this->has('invoice_info_date')): ?>
                    <tr>
                        <td>Date</td>
                        <td><?php echo substr($this->invoice['invoice_date'], 0 ,10); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($this->has('invoice_info_number')): ?>
                    <tr>
                        <td>N° de facture:</td>
                        <td><?php echo $this->invoice['invoice_number']; ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($this->has('invoice_info_number_alt')): ?>
                    <tr>
                        <td>Identifiant de facture externe:</td>
                        <td><?php echo $this->invoice['invoice_number_alt']; ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($this->has('invoice_info_order_amount')): ?>
                    <tr>
                        <td>Montant total:</td>
                        <td><?php echo $cartModel['priceOrderGrandTotal']; ?></td>
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
                        <td><?php echo $this->invoice['currency_iso_code']; ?></td>
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