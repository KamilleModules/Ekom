<?php




$prc = "Ekom.kamille.ek_carrier_has_condition";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_carrier_has_condition.carrier_id',
            'ek_carrier_has_condition.condition_id',
            'ek_carrier.id',
            'ek_condition.type',
            'action',
        ],
        'hidden' => [
            'ek_carrier_has_condition.carrier_id',
            'ek_carrier_has_condition.condition_id',
        ],
        'ric' => [
            'ek_carrier_has_condition.carrier_id',
            'ek_carrier_has_condition.condition_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_carrier_has_condition',
            ],
        ],
    ],
]);
