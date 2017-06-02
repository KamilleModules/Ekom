<?php




$prc = "Ekom.kamille.ek_payment_method_shop";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_payment_method_shop.payment_method_id',
            'ek_payment_method_shop.shop_id',
            'ek_payment_method_shop.active',
            'ek_payment_method.label',
            'ek_shop.label',
            'action',
        ],
        'hidden' => [
            'ek_payment_method_shop.payment_method_id',
            'ek_payment_method_shop.shop_id',
        ],
        'ric' => [
            'ek_payment_method_shop.payment_method_id',
            'ek_payment_method_shop.shop_id',
            'ek_payment_method_shop.active',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_payment_method_shop',
            ],
        ],
    ],
]);
