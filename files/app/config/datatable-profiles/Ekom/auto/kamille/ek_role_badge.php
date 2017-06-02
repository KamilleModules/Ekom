<?php




$prc = "Ekom.kamille.ek_role_badge";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_role_badge.id',
            'ek_role_badge.label',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_role_badge.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_role_badge',
            ],
        ],
    ],
]);
