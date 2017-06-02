<?php




$prc = "Ekom.kamille.ek_store";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_store.id',
            'ek_store.label',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_store.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_store',
            ],
        ],
    ],
]);
