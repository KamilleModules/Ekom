<?php




$prc = "Ekom.kamille.ek_order_status_shop";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_order_status_shop.order_status_id',
            'ek_order_status_shop.shop_id',
            'ek_order_status_shop.color',
            'ek_order_status.label',
            'ek_shop.label',
            'action',
        ],
        'hidden' => [
            'ek_order_status_shop.order_status_id',
            'ek_order_status_shop.shop_id',
        ],
        'ric' => [
            'ek_order_status_shop.order_status_id',
            'ek_order_status_shop.shop_id',
            'ek_order_status_shop.color',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_order_status_shop',
            ],
        ],
    ],
]);
