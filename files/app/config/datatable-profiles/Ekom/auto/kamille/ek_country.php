<?php




$prc = "Ekom.kamille.ek_country";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_country.id',
            'ek_country.iso_code',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_country.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_country',
            ],
        ],
    ],
]);
