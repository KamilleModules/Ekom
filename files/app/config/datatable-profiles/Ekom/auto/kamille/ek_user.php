<?php




$prc = "Ekom.kamille.ek_user";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_user.id',
            'ek_user_group.label',
            'ek_user.email',
            'ek_user.pass',
            'ek_user.base_shop_id',
            'ek_user.date_creation',
            'ek_user.active',
            'ek_address.type',
            'ek_user.mobile',
            'ek_user.phone',
            'ek_user.pro',
            'action',
        ],
        'hidden' => [
            'ek_user.user_group_id',
            'ek_user.main_address_id',
        ],
        'ric' => [
            'ek_user.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_user',
            ],
        ],
    ],
]);
