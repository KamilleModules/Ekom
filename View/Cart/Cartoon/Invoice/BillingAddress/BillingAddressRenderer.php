<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\BillingAddress;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class BillingAddressRenderer extends BaseRenderer
{

    /**
     * @see EkomModels::invoiceModel()
     */
    private $invoice;

    public function __construct()
    {
        parent::__construct();

        $this->invoice = [];
        $this->columns = [
            'billing_address',
            'billing_address_phone',
            'billing_address_email',
        ];
    }

    public function setInvoice(array $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }


    public function render()
    {
        $b = $this->invoice['billing_address'];
        $u = $this->invoice['user_info'];


        ?>
        <div class="user-billing-address">
            <h4>Facturé à:</h4>
            <?php if (true === $this->has('billing_address')): ?>
                <div class="address-line"><?php echo $b['fName']; ?></div>
                <div class="address-line"><?php echo $b['address']; ?></div>
                <div class="address-line"><?php echo $b['postcode']; ?>&nbsp;<?php echo $b['city']; ?></div>
            <?php endif; ?>

            <?php if (true === $this->has('billing_address_phone')): ?>
                <div class="address-line"><?php echo $b['phone']; ?></div>
            <?php endif; ?>
            <?php if (true === $this->has('billing_address_email')): ?>
                <div class="address-line"><?php echo $u['email']; ?></div>
            <?php endif; ?>
        </div>
        <?php
    }
//


}