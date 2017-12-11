<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\InvoiceHeader;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class OrderHeaderRenderer extends BaseRenderer
{

    /**
     * @see EkomModels::invoiceModel()
     */
    private $order;
    private $title;

    public function __construct()
    {
        parent::__construct();

        $this->title = "Devis";
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


    public function setOrder(array $order)
    {
        $this->order = $order;
        return $this;
    }


    public function render()
    {
        $orderDetails = $this->order['order_details'];
        $paymentMethod = $orderDetails['payment_method_name'];
        $cartModel = $orderDetails['cartModel'];
        $paymentDetails = $orderDetails['payment_method_details'];
        ?>
        <div class="invoice-header">
            <?php if ($this->has('invoice_title')): ?>
                <div class="invoice-title"><?php echo $this->title; ?></div>
            <?php endif; ?>
            <div class="invoice-info-properties">
                <?php if ($this->has('invoice_date')): ?>
                    <div class="property">
                        <span class="key">Date:</span>
                        <span class="value"><?php echo substr($this->order['date'], 0, 10); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($this->has('invoice_number')): ?>
                    <div class="property">
                        <span class="key">N° du bon de commande:</span>
                        <span class="value"><?php echo $this->order['reference']; ?></span>
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
                        <span class="value"><?php echo $this->order['currency_iso_code']; ?></span>
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