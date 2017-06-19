<?php




$prc = "Ekom.kamille.ek_order";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_order.id',
            'ek_user.email',
            'ek_order.reference',
            'ek_order.date',
            'ek_order.tracking_number',
            'ek_order.user_info',
            'ek_order.shop_info',
            'ek_order.shipping_address',
            'ek_order.billing_address',
            'ek_order.order_details',
            'action',
        ],
        'hidden' => [
            'ek_order.user_id',
        ],
        'ric' => [
            'ek_order.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_order',
            ],
        ],
    ],
]);
