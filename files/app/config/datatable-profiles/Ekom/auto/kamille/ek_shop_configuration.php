<?php




$prc = "Ekom.kamille.ek_shop_configuration";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_shop_configuration.shop_id',
            'ek_shop_configuration.key',
            'ek_shop_configuration.value',
            'ek_shop.label',
            'action',
        ],
        'hidden' => [
            'ek_shop_configuration.shop_id',
        ],
        'ric' => [
            'ek_shop_configuration.shop_id',
            'ek_shop_configuration.key',
            'ek_shop_configuration.value',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_shop_configuration',
            ],
        ],
    ],
]);
