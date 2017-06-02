<?php




$prc = "Ekom.kamille.ek_role_profile";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_role_profile.id',
            'ek_role_profile.label',
            'ek_backoffice_user.email',
            'action',
        ],
        'hidden' => [
            'ek_role_profile.backoffice_user_id',
        ],
        'ric' => [
            'ek_role_profile.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_role_profile',
            ],
        ],
    ],
]);
