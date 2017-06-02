<?php




$prc = "Ekom.kamille.ek_state";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_state.id',
            'ek_state.iso_code',
            'ek_state.label',
            'ek_country.iso_code',
            'action',
        ],
        'hidden' => [
            'ek_state.country_id',
        ],
        'ric' => [
            'ek_state.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_state',
            ],
        ],
    ],
]);
