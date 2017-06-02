<?php




$prc = "Ekom.kamille.ek_carrier";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_carrier.id',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_carrier.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_carrier',
            ],
        ],
    ],
]);
