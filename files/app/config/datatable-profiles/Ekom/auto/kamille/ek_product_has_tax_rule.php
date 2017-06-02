<?php




$prc = "Ekom.kamille.ek_product_has_tax_rule";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_product_has_tax_rule.product_id',
            'ek_product_has_tax_rule.tax_rule_id',
            'ek_product.product_reference_id',
            'ek_tax_rule.condition',
            'action',
        ],
        'hidden' => [
            'ek_product_has_tax_rule.product_id',
            'ek_product_has_tax_rule.tax_rule_id',
        ],
        'ric' => [
            'ek_product_has_tax_rule.product_id',
            'ek_product_has_tax_rule.tax_rule_id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_product_has_tax_rule',
            ],
        ],
    ],
]);
