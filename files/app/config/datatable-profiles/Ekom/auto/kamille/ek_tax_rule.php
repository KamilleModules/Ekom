<?php




$prc = "Ekom.kamille.ek_tax_rule";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_tax_rule.id',
            'ek_tax.reduction',
            'ek_tax_rule.condition',
            'action',
        ],
        'hidden' => [
            'ek_tax_rule.tax_id',
        ],
        'ric' => [
            'ek_tax_rule.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_tax_rule',
            ],
        ],
    ],
]);
