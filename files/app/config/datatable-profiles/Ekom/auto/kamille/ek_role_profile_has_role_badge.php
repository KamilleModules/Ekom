<?php




$prc = "Ekom.kamille.ek_role_profile_has_role_badge";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_role_profile_has_role_badge.role_profile_id',
            'ek_role_profile_has_role_badge.role_badge_id',
            'ek_role_profile.label',
            'ek_role_badge.label',
            'action',
        ],
        'hidden' => [
            'ek_role_profile_has_role_badge.role_profile_id',
            'ek_role_profile_has_role_badge.role_badge_id',
        ],
        'ric' => [
            'ek_role_profile_has_role_badge.role_profile_id',
            'ek_role_profile_has_role_badge.role_badge_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_role_profile_has_role_badge',
            ],
        ],
    ],
]);
