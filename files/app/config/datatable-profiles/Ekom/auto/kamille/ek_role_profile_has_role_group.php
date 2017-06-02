<?php




$prc = "Ekom.kamille.ek_role_profile_has_role_group";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_role_profile_has_role_group.role_profile_id',
            'ek_role_profile_has_role_group.role_group_id',
            'ek_role_profile.label',
            'ek_role_group.label',
            'action',
        ],
        'hidden' => [
            'ek_role_profile_has_role_group.role_profile_id',
            'ek_role_profile_has_role_group.role_group_id',
        ],
        'ric' => [
            'ek_role_profile_has_role_group.role_profile_id',
            'ek_role_profile_has_role_group.role_group_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_role_profile_has_role_group',
            ],
        ],
    ],
]);
