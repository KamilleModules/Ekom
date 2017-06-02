<?php




$prc = "Ekom.kamille.ek_order_status";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_order_status.id',
            'ek_order_status.label',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_order_status.lang_id',
        ],
        'ric' => [
            'ek_order_status.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_order_status',
            ],
        ],
    ],
]);
