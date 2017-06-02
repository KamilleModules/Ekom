<?php




$prc = "Ekom.kamille.ek_discount_rule_lang";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_discount_rule_lang.discount_rule_id',
            'ek_discount_rule_lang.label',
            'ek_discount_rule_lang.lang_id',
            'ek_discount_rule.type',
            'ek_lang.label',
            'action',
        ],
        'hidden' => [
            'ek_discount_rule_lang.discount_rule_id',
            'ek_discount_rule_lang.lang_id',
        ],
        'ric' => [
            'ek_discount_rule_lang.discount_rule_id',
            'ek_discount_rule_lang.label',
            'ek_discount_rule_lang.lang_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_discount_rule_lang',
            ],
        ],
    ],
]);
