<?php




$prc = "Ekom.kamille.ek_currency_shop";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_currency_shop.id',
            'ek_currency.iso_code',
            'ek_shop.label',
            'ek_currency_shop.exchange_rate',
            'ek_currency_shop.active',
            'action',
        ],
        'hidden' => [
            'ek_currency_shop.currency_id',
            'ek_currency_shop.shop_id',
        ],
        'ric' => [
            'ek_currency_shop.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_currency_shop',
            ],
        ],
    ],
]);
