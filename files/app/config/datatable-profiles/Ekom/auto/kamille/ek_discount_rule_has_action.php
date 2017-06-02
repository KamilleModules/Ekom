<?php




$prc = "Ekom.kamille.ek_discount_rule_has_action";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_discount_rule_has_action.discount_rule_id',
            'ek_discount_rule_has_action.action_id',
            'ek_discount_rule.type',
            'ek_action.source',
            'action',
        ],
        'hidden' => [
            'ek_discount_rule_has_action.discount_rule_id',
            'ek_discount_rule_has_action.action_id',
        ],
        'ric' => [
            'ek_discount_rule_has_action.discount_rule_id',
            'ek_discount_rule_has_action.action_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_discount_rule_has_action',
            ],
        ],
    ],
]);
