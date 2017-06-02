<?php




$prc = "Ekom.kamille.ek_shop";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_shop.id',
            'ek_shop.label',
            'ek_lang.label',
            'ek_currency.iso_code',
            'ek_timezone.name',
            'action',
        ],
        'hidden' => [
            'ek_shop.lang_id',
            'ek_shop.currency_id',
            'ek_shop.timezone_id',
        ],
        'ric' => [
            'ek_shop.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_shop',
            ],
        ],
    ],
]);
