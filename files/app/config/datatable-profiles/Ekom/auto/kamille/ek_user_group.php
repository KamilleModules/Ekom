<?php




$prc = "Ekom.kamille.ek_user_group";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_user_group.id',
            'ek_user_group.label',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_user_group.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_user_group',
            ],
        ],
    ],
]);
