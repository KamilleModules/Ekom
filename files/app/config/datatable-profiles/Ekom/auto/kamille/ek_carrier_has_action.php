<?php




$prc = "Ekom.kamille.ek_carrier_has_action";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_carrier_has_action.carrier_id',
            'ek_carrier_has_action.action_id',
            'ek_carrier.id',
            'ek_action.source',
            'action',
        ],
        'hidden' => [
            'ek_carrier_has_action.carrier_id',
            'ek_carrier_has_action.action_id',
        ],
        'ric' => [
            'ek_carrier_has_action.carrier_id',
            'ek_carrier_has_action.action_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_carrier_has_action',
            ],
        ],
    ],
]);
