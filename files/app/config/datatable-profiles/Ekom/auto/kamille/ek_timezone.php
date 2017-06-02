<?php




$prc = "Ekom.kamille.ek_timezone";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_timezone.id',
            'ek_timezone.name',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_timezone.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_timezone',
            ],
        ],
    ],
]);
