<?php




$prc = "Ekom.kamille.ek_currency";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_currency.id',
            'ek_currency.iso_code',
            'ek_currency.symbol',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_currency.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_currency',
            ],
        ],
    ],
]);
