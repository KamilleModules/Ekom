<?php




$prc = "Ekom.kamille.ek_cart";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_cart.id',
            'ek_cart.items',
            'ek_user.email',
            'action',
        ],
        'hidden' => [
            'ek_cart.user_id',
        ],
        'ric' => [
            'ek_cart.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_cart',
            ],
        ],
    ],
]);
