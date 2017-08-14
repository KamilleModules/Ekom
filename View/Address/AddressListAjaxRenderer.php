<?php


namespace Module\Ekom\View\Address;


class AddressListAjaxRenderer
{

    public static function create()
    {
        return new static();
    }

    public function render(array $m)
    {
        ?>
        <div class="choose-address-ajax">
            <div class="title">Choisissez une adresse</div>
            <div class="address-list">
                <?php for ($i = 1; $i <= 3; $i++): ?>
                    <div class="item">
                        <div class="address-info">
                            <label for="choose-address-6">TRAVAIL</label>
                            <div class="line-1">5, impasse de la fleur</div>
                            <div class="line-2">37190 SACHÉ</div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php
    }
}