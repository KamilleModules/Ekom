<?php




$prc = "Ekom.kamille.ek_condition";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_condition.id',
            'ek_condition.type',
            'ek_condition.combinator',
            'ek_condition.negation',
            'ek_condition.start_group',
            'ek_condition.end_group',
            'ek_condition.left_operand',
            'ek_condition.operator',
            'ek_condition.right_operand',
            'ek_condition.right_operand2',
            'action',
        ],
        'hidden' => [
        ],
        'ric' => [
            'ek_condition.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_condition',
            ],
        ],
    ],
]);
