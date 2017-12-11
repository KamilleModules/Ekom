<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\CompanyHeader;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class CompanyHeaderRenderer extends BaseRenderer
{

    /**
     * @see EkomModels::shopPhysicalAddress()
     */
    protected $sellerAddress;
    protected $logoUri;
    protected $logoAlt;

    public function __construct()
    {
        parent::__construct();
        $this->sellerAddress = [];
        $this->logoUri = "";
        $this->logoAlt = "logo";
        $this->columns = [
            'logo',
            'companyName',
            'address',
            'phone',
//            'phoneInternational', @todo-ling: ?
        ];
    }



    public function setSellerAddress(array $sellerAddress)
    {
        $this->sellerAddress = $sellerAddress;
        return $this;
    }

    public function setLogo($logoUri, $alt = null)
    {
        $this->logoUri = $logoUri;
        if (null !== $alt) {
            $this->logoAlt = $alt;
        }
        return $this;
    }


    public function render()
    {
        ?>
        <div class="company-header">
            <?php if (in_array("logo", $this->columns, true)): ?>
                <div class="company-logo">
                    <img src="<?php echo htmlspecialchars($this->logoUri); ?>"
                         alt="<?php echo htmlspecialchars($this->logoAlt); ?>">
                </div>
            <?php endif; ?>
            <?php if (in_array("companyName", $this->columns, true)): ?>
                <div class="company-title"><?php echo $this->sellerAddress['last_name']; ?></div>
            <?php endif; ?>
            <?php if (in_array("address", $this->columns, true)): ?>
                <div class="address-line"><?php echo $this->sellerAddress['address']; ?></div>
                <div class="address-line"><?php echo $this->sellerAddress['postcode']; ?><?php echo $this->sellerAddress['city']; ?></div>
                <div class="address-line"><?php echo $this->sellerAddress['country']; ?></div>
            <?php endif; ?>
            <?php if (in_array("phone", $this->columns, true)): ?>
                <div class="address-line"><?php echo $this->sellerAddress['phone']; ?></div>
            <?php endif; ?>
        </div>
        <?php
    }
}