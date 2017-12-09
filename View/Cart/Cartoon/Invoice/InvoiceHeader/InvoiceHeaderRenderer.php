<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\InvoiceHeader;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class InvoiceHeaderRenderer extends BaseRenderer
{

    /**
     * @see EkomModels::invoiceModel()
     */
    private $invoice;
    private $title;

    public function __construct()
    {
        parent::__construct();

        $this->title = "Facture";
        $this->columns = [
            'invoice_title',
            'invoice_date',
            'invoice_number',
            'invoice_number_alt',
            'order_amount',
            'order_payment_method',
            'invoice_currency',
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
        <div class="invoice-header">
            <?php if ($this->has('invoice_title')): ?>
                <div class="invoice-title"><?php echo $this->title; ?></div>
            <?php endif; ?>
            <div class="invoice-info-properties">
                <?php if ($this->has('invoice_date')): ?>
                    <div class="property">
                        <span class="key">Date:</span>
                        <span class="value"><?php echo substr($this->invoice['invoice_date'], 0, 10); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($this->has('invoice_number')): ?>
                    <div class="property">
                        <span class="key">N° de facture:</span>
                        <span class="value"><?php echo $this->invoice['invoice_number']; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($this->has('invoice_number_alt')): ?>
                    <div class="property">
                        <span class="key">Identifiant de facture externe:</span>
                        <span class="value"><?php echo $this->invoice['invoice_number_alt']; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($this->has('order_amount')): ?>
                    <div class="property">
                        <span class="key">Montant total:</span>
                        <span class="value"><?php echo $cartModel['priceOrderGrandTotal']; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($this->has('order_payment_method')): ?>
                    <div class="property">
                        <span class="key">Méthode de paiement:</span>
                        <?php if ('credit_card_wallet' === $paymentMethod):
                            $cardLastFourDigits = $paymentDetails['credit_card']['last_four_digits']; ?>
                            <span class="value">Carte bleue se terminant par <?php echo $cardLastFourDigits; ?></span>
                        <?php elseif ('transfer' === $paymentMethod): ?>
                            <span class="value">Virement</span>
                        <?php else: ?>
                            <span class="value">Inconnu</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->has('invoice_currency')): ?>
                    <div class="property">
                        <span class="key">Devise:</span>
                        <span class="value"><?php echo $this->invoice['currency_iso_code']; ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    //--------------------------------------------
    //
    //--------------------------------------------


}