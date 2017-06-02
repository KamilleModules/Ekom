<?php




$prc = "Ekom.kamille.ek_discount_rule_has_condition";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_discount_rule_has_condition.discount_rule_id',
            'ek_discount_rule_has_condition.condition_id',
            'ek_discount_rule.type',
            'ek_condition.type',
            'action',
        ],
        'hidden' => [
            'ek_discount_rule_has_condition.discount_rule_id',
            'ek_discount_rule_has_condition.condition_id',
        ],
        'ric' => [
            'ek_discount_rule_has_condition.discount_rule_id',
            'ek_discount_rule_has_condition.condition_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_discount_rule_has_condition',
            ],
        ],
    ],
]);
