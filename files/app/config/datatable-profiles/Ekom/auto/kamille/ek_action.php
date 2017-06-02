<?php




$prc = "Ekom.kamille.ek_action";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_action.id',
            'ek_action.source',
            'ek_action.source2',
            'ek_action.operator',
            'ek_action.target',
            'ek_action.target2',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_action.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_action',
            ],
        ],
    ],
]);
