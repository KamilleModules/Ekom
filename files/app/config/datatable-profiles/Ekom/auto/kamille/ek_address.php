<?php




$prc = "Ekom.kamille.ek_address";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_address.id',
            'ek_address.type',
            'ek_address.city',
            'ek_address.postcode',
            'ek_address.address',
            'ek_address.active',
            'ek_state.iso_code',
            'ek_country.iso_code',
            'action',
        ],
        'hidden' => [
            'ek_address.state_id',
            'ek_address.country_id',
        ],
        'ric' => [
            'ek_address.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_address',
            ],
        ],
    ],
]);
