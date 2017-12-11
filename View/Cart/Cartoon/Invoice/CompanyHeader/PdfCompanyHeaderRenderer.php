<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\CompanyHeader;


class PdfCompanyHeaderRenderer extends CompanyHeaderRenderer
{
    public function render()
    {
        if (in_array("logo", $this->columns, true)): ?>
            <img src="<?php echo htmlspecialchars($this->logoUri); ?>"
                 alt="<?php echo htmlspecialchars($this->logoAlt); ?>">
            <br>
        <?php endif; ?>


        <?php if (in_array("companyName", $this->columns, true)): ?>
        <?php echo $this->sellerAddress['last_name']; ?>
        <br>
    <?php endif; ?>
        <?php if (in_array("address", $this->columns, true)): ?>
        <?php echo $this->sellerAddress['address']; ?>
        <br>
        <?php echo $this->sellerAddress['postcode']; ?><?php echo $this->sellerAddress['city']; ?>
        <br>
        <?php echo $this->sellerAddress['country']; ?>
        <br>
    <?php endif; ?>
        <?php if (in_array("phone", $this->columns, true)): ?>
        <?php echo $this->sellerAddress['phone']; ?>
    <?php endif;
    }
}