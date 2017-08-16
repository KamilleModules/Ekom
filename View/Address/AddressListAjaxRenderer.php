<?php


namespace Module\Ekom\View\Address;


class AddressListAjaxRenderer
{

    public static function create()
    {
        return new static();
    }

    public function render(array $addresses)
    {
        ?>
        <div class="choose-address-ajax">
            <div class="title">Choisissez une adresse</div>
            <div class="address-list">
                <?php foreach ($addresses as $a): ?>
                    <div class="item ajax-address-li-trigger" data-id="<?php echo $a['address_id']; ?>">
                        <div class="address-info ajax-address-li-trigger">
                            <label class="ajax-address-li-trigger" for="choose-address-<?php echo $a['address_id']; ?>"><?php echo $a['fName']; ?></label>
                            <div class="line-1 ajax-address-li-trigger"><?php echo $a['address']; ?></div>
                            <div class="line-2 ajax-address-li-trigger"><?php echo $a['postcode'] . ' ' . $a['city']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}