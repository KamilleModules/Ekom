<?php




$prc = "Ekom.kamille.ek_order_has_order_status";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_order_has_order_status.order_id',
            'ek_order_has_order_status.order_status_id',
            'ek_order.reference',
            'ek_order_status.label',
            'ek_order_has_order_status.date',
            'action',
        ],
        'hidden' => [
            'ek_order_has_order_status.order_id',
            'ek_order_has_order_status.order_status_id',
        ],
        'ric' => [
            'ek_order_has_order_status.order_id',
            'ek_order_has_order_status.order_status_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_order_has_order_status',
            ],
        ],
    ],
]);
