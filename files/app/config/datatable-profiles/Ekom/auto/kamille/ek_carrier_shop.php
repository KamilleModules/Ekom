<?php




$prc = "Ekom.kamille.ek_carrier_shop";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_carrier_shop.carrier_id',
            'ek_carrier_shop.shop_id',
            'ek_carrier_shop.active',
            'ek_carrier.id',
            'ek_shop.label',
            'action',
        ],
        'hidden' => [
            'ek_carrier_shop.carrier_id',
            'ek_carrier_shop.shop_id',
        ],
        'ric' => [
            'ek_carrier_shop.carrier_id',
            'ek_carrier_shop.shop_id',
            'ek_carrier_shop.active',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_carrier_shop',
            ],
        ],
    ],
]);
