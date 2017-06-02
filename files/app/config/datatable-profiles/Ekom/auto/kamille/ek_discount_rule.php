<?php




$prc = "Ekom.kamille.ek_discount_rule";
include __DIR__ . "/../../../NullosAdmin/inc/common.php";


$profile = array_replace_recursive($profile, [
    'model' => [
        'headers' => [
            'ek_discount_rule.id',
            'ek_discount_rule.type',
            'ek_shop.label',
            'action',
        ],
        'hidden' => [
            'ek_discount_rule.shop_id',
        ],
        'ric' => [
            'ek_discount_rule.id',
        ],
        'actionButtons' => [
            'addItem' => [
                'label' => 'Add Ek_discount_rule',
            ],
        ],
    ],
]);
